<?php


namespace App\Controller;


use App\Entity\CompteurFamilleMoisPlanning;
use App\Entity\MoisPlanning;
use App\Entity\PresenceEnfant;
use App\Repository\CoefGardeFamilleRepository;
use App\Repository\CompteurFamilleMoisPlanningRepository;
use App\Repository\ContratRepository;
use App\Repository\FamilleRepository;
use App\Repository\GardeRepository;
use App\Repository\JourPlanningRepository;
use App\Repository\PresenceEnfantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MoisPlanningController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $entityManager;
    /** @var JourPlanningRepository */
    private $jourPlanningRepository;
    /** @var FamilleRepository */
    private $familleRepository;
    /** @var CompteurFamilleMoisPlanningRepository */
    private $compteurFamilleMoisPlanningRepository;
    /** @var CoefGardeFamilleRepository */
    private $coefGardeFamilleRepository;
    /** @var ContratRepository */
    private $contratRepository;
    /** @var PresenceEnfantRepository */
    private $presenceEnfantRepository;
    /** @var GardeRepository */
    private $gardeRepository;


    public function __construct(EntityManagerInterface $entityManager, JourPlanningRepository $jourPlanningRepository, FamilleRepository $familleRepository, CompteurFamilleMoisPlanningRepository $compteurFamilleMoisPlanningRepository, CoefGardeFamilleRepository $coefGardeFamilleRepository, ContratRepository $contratRepository, PresenceEnfantRepository $presenceEnfantRepository, GardeRepository $gardeRepository)
    {
        $this->entityManager = $entityManager;
        $this->jourPlanningRepository = $jourPlanningRepository;
        $this->familleRepository = $familleRepository;
        $this->compteurFamilleMoisPlanningRepository = $compteurFamilleMoisPlanningRepository;
        $this->coefGardeFamilleRepository = $coefGardeFamilleRepository;
        $this->contratRepository = $contratRepository;
        $this->presenceEnfantRepository = $presenceEnfantRepository;
        $this->gardeRepository = $gardeRepository;
    }


    /**
     * @throws Exception
     */
    public function recalculerCompteursFamilles(MoisPlanning $data): MoisPlanning
    {
        $moisPlanning = $data;
        $moisPlanningPrecedent = $moisPlanning->moisPlanningPrecedent;

        try {
            $this->entityManager->beginTransaction();
            // Liste des familles présentes sur ce mois de planning
            $familles = $this->familleRepository->findBetweenDateDebutAndDateFin($moisPlanning->dateDebut, $moisPlanning->dateFin);
            // On supprime tous les compteurs mois des familles non listées
            $this->compteurFamilleMoisPlanningRepository->deleteByMoisPlanningAndFamilleNotIn($moisPlanning, $familles);
            // Pour chacune de ces familles, on récupère le compteur du mois précédent
            $compteursMoisPrecedentByFamilleId = [];
            if (!empty($moisPlanningPrecedent)) {
                foreach ($familles as $famille) {
                    $compteursMoisPrecedentByFamilleId[$famille->getId()] = $this->compteurFamilleMoisPlanningRepository->findByFamilleAndMoisPlanning($famille, $moisPlanningPrecedent);
                }
            }
            // Pour chacune de ces familles, si elle n'a pas de compteur pour ce mois, on le crée
            $compteursMoisByFamilleId = [];
            foreach ($familles as $famille) {
                $compteurMois = $this->compteurFamilleMoisPlanningRepository->findByFamilleAndMoisPlanning($famille, $moisPlanning);
                if (empty($compteurMois)) {
                    $compteurMois = new CompteurFamilleMoisPlanning();
                    $compteurMois->famille = $famille;
                    $compteurMois->moisPlanning = $moisPlanning;
                    $compteurMois->compteur = 0;
                    $this->entityManager->persist($compteurMois);
                }
                // Ré-initialisation des différentes variables de ce compteur mois
                $compteurMoisPrecedent = $compteursMoisPrecedentByFamilleId[$famille->getId()];
                // Les quotas de jours d'absence
                if (!empty($compteurMoisPrecedent)) {
                    $compteurMois->quotaJoursAbsence = !is_null($compteurMoisPrecedent->quotaJoursAbsenceForce) ? $compteurMoisPrecedent->quotaJoursAbsenceForce : $compteurMoisPrecedent->quotaJoursAbsence;
                    $compteurMois->compteur = !is_null($compteurMoisPrecedent->compteurForce) ? $compteurMoisPrecedent->compteurForce : $compteurMoisPrecedent->compteur;
                } else {
                    $compteurMois->quotaJoursAbsence = 20; // TODO initialiser cette valeur avec une table de paramètres pour l'application
                    $compteurMois->compteur = 0;
                }
                $compteurMois->coefTotal = 0;

                $compteursMoisByFamilleId[$famille->getId()] = $compteurMois;
            }
            $moisPlanning->compteursFamilles = array_values($compteursMoisByFamilleId);

            /// Calcul des compteurs du mois ///
            $joursPlanning = $this->jourPlanningRepository->findByMoisPlanningCode($moisPlanning->code);
            $coefTotal = 0;
            $nbJoursAbsencesSemaineByEnfantId = [];
            $nbJoursCrecheOuverte = 0;
            // On itère sur chaque jour du mois de planning
            foreach ($joursPlanning as $jourPlanning) {
                // Si c'est un lundi, on reset le nb de jours d'absences enregistré pour chaque enfant par semaine
                $jourPlanningDate = $jourPlanning->date;
                if ($jourPlanningDate->format('N') === '1') { // https://stackoverflow.com/a/29194122/535203
                    $nbJoursAbsencesSemaineByEnfantId = [];
                }
                // On vérifie que pour ce jour, la crèche n'est pas fermée pour les enfants : sinon on ignore ce jour dans les calculs
                if ($jourPlanning->crecheOuvertePourEnfants) {
                    $nbJoursCrecheOuverte++;
                    foreach ($familles as $famille) {
                        // Pour chaque famille, on va calculer son coefficient de la journée
                        $compteurMois = $compteursMoisByFamilleId[$famille->getId()];
                        // Avant cela on ré-initialise le quota de nombre de jours d'absence si on doit le faire pour ce jour
                        if ($jourPlanningDate == $moisPlanning->reinitQuotaJoursAbsenceDate && !isset($moisPlanning->reinitQuotaJoursAbsenceNombre)) {
                            $compteurMois->quotaJoursAbsence = $moisPlanning->reinitQuotaJoursAbsenceNombre;
                        }
                        // On récupère les coefficients forcés d'abord
                        $coefs = $this->coefGardeFamilleRepository->findByFamilleAndDateOrderByCoef($famille, $jourPlanningDate);
                        if (!empty($coefs)) {
                            $coef = $coefs[0]->coef; // on prend le coefficient le + petit du jour
                        } else {
                            $coef = 1.0; // Sinon par défaut il est de 1
                        }
                        if ($coef !== 0.0) {
                            // Le coef actuel n'est pas encore à 0, on va donc le comparer avec le coef que la famille aurait dû avoir ce jour là, donc on va regarder la présence de ses enfants comparée à leur présence théorique
                            // Tout d'abord on récupère la liste des contrats actifs (= de ses enfants pouvant être présents à la crèche) à la date du jour pour cette famille
                            $contrats = $this->contratRepository->findByFamilleAndDate($famille, $jourPlanningDate);
                            $nbEnfantsPresents = 0;
                            foreach ($contrats as $contrat) {
                                // Pour chaque enfant, on vérifie s'il est absent ce jour
                                $enfant = $contrat->enfant;
                                $presenceEnfant = $this->presenceEnfantRepository->findOneByJourPlanningAndEnfant($jourPlanning, $enfant);
                                if (isset($presenceEnfant) && isset($presenceEnfant->present) && !$presenceEnfant->present) {
                                    // Cet enfant est absent ce jour, on le rajoute à son nombre de jour d'absence de la semaine
                                    $enfantId = $enfant->getId();
                                    if (isset($nbJoursAbsencesSemaineByEnfantId[$enfantId])) {
                                        $nbJoursAbsencesSemaineByEnfantId[$enfantId] += 1;
                                    } else {
                                        $nbJoursAbsencesSemaineByEnfantId[$enfantId] = 1;
                                    }
                                    if (5 - $nbJoursAbsencesSemaineByEnfantId[$enfantId] < $contrat->nbJoursParSemaine) {
                                        // Cette absence sort de son contrat, elle va donc impacter le quota de jours d'absences de la famille
                                        $impactNbJoursAbsences = 1 / sizeof($contrats); // exemple : si 3 enfants pour cette famille, on retire 1/3 de jour d'absence à chaque jour d'absence d'1 enfant
                                        if ($compteurMois->quotaJoursAbsence - $impactNbJoursAbsences < 0) {
                                            // la famille n'a plus assez de quota de jours d'absence : cette absence ne va donc pas compter, c'est à dire que le coefficient de la famille pour cette journée tiendra compte de la présence de cet enfant
                                            $nbEnfantsPresents++;
                                        } else {
                                            // la famille a assez de quota de jours d'absence : on retire l'impact d'absence de cet enfant de son quota, le coefficient de la famille pour cette journée tiendra compte de l'absence de cet enfant
                                            $compteurMois->quotaJoursAbsence -= $impactNbJoursAbsences;
                                        }
                                    }
                                } else {
                                    // L'enfant est présent ce jour
                                    $nbEnfantsPresents++;
                                }
                            }
                            if ($nbEnfantsPresents === 0) {
                                // Aucun enfant présent ce jour, le coef de cette famille passe à 0
                                $coef = 0.0;
                            } else {
                                // On multiplie le coefficient défini pour ce jour par 1 si 1 enfant, 1,5 si 2 enfants, 2 si 3 enfants etc...
                                $coef *= ($nbEnfantsPresents / 2) + 0.5;
                            }
                        }
                        $compteurMois->coefTotal += $coef;
                        $coefTotal += $coef;
                    }
                }
            }

            // Maintenant on va récupérer le nombre de secondes de garde effectuées sur le mois par la famille, ainsi que le nombre de secondes
            // qu'elle devrait faire sur le mois en fonction de son coef, l'évolution de son compteur, et son nouveau compteur
            // ainsi que tout ceci si l'on tient compte des gardes non affectées actuellement
            $tempsGardesTotal = 0;
            foreach ($familles as $famille) {
                $compteurMois = $compteursMoisByFamilleId[$famille->getId()];
                $compteurMois->tempsGardesTotal = $this->gardeRepository->findSumSecondsByFamilleAndMoisPlanning($famille, $moisPlanning);
                $compteurMois->coef = $compteurMois->coefTotal / $nbJoursCrecheOuverte;
                $tempsGardesTotal += $compteurMois->tempsGardesTotal;
            }
            $tempsGardesTotalNA = $this->gardeRepository->findSumSecondsByMoisPlanning($moisPlanning);
            foreach ($familles as $famille) {
                $compteurMois = $compteursMoisByFamilleId[$famille->getId()];
                $compteurMois->tempsARealiser = $tempsGardesTotal / $nbJoursCrecheOuverte * $compteurMois->coef;
                $compteurMois->evolutionCompteur = $compteurMois->tempsGardesTotal - $compteurMois->tempsARealiser;
                $compteurPrecedent = $compteurMois->compteur;
                $compteurMois->compteur = $compteurPrecedent + $compteurMois->evolutionCompteur;
                // Calculs en tenant compte des gardes non attribuées
                $compteurMois->tempsARealiserNA = $tempsGardesTotalNA / $nbJoursCrecheOuverte * $compteurMois->coef;
                $compteurMois->evolutionCompteurNA = $compteurMois->tempsGardesTotal - $compteurMois->tempsARealiserNA;
                $compteurMois->compteurNA = $compteurPrecedent + $compteurMois->evolutionCompteurNA;
            }

            $this->entityManager->commit();
        } catch (Exception $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }

        return $moisPlanning;
    }
}