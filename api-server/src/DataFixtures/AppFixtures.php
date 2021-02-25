<?php

namespace App\DataFixtures;

use App\Entity\Enfant;
use App\Entity\EnfantGroupeEnfant;
use App\Entity\Famille;
use App\Entity\Garde;
use App\Entity\GroupeEnfant;
use App\Entity\JourPlanning;
use App\Entity\MoisPlanning;
use App\Entity\PresenceEnfant;
use App\Entity\PresencePro;
use App\Entity\Pro;
use App\Enum\AbsenceEnfantType;
use App\Enum\AbsenceProType;
use DateInterval;
use DatePeriod;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use function Symfony\Component\DependencyInjection\Loader\Configurator\iterator;

class AppFixtures extends Fixture
{

    private $faker;

    const NOMBRE_DE_FAMILLES = 20;
    const NOMBRE_D_ENFANTS = 21;
    const NOMBRE_DE_GROUPES_D_ENFANTS = 3;
    const NOMBRE_DE_PROS = 6;


    public function __construct()
    {
        $this->faker = Faker\Factory::create('fr_FR');
        $this->faker->seed(1);
    }


    public function load(ObjectManager $manager)
    {
        $faker = $this->faker;

        // Création des groupes d'enfants
        $groupesEnfants = [];
        $groupeEnfant = new GroupeEnfant();
        $groupeEnfant->nom = 'Bébés';
        $manager->persist($groupeEnfant);
        $groupesEnfants[] = $groupeEnfant;

        $groupeEnfant = new GroupeEnfant();
        $groupeEnfant->nom = 'Moyens';
        $manager->persist($groupeEnfant);
        $groupesEnfants[] = $groupeEnfant;

        $groupeEnfant = new GroupeEnfant();
        $groupeEnfant->nom = 'Grands';
        $manager->persist($groupeEnfant);
        $groupesEnfants[] = $groupeEnfant;

        // Création des familles
        $familles = [];
        $enfants = [];
        for ($i = 0; $i < AppFixtures::NOMBRE_DE_FAMILLES; $i++) {
            $famille = new Famille();
            $famille->nom = $faker->firstName();
            $famille->dateEntree = $faker->dateTimeBetween('-1 year', 'now');
            $famille->dateSortie = $faker->dateTimeBetween('now', '+1 year');
            $manager->persist($famille);
            $familles[] = $famille;

            // Création des enfants
            $nbEnfants = $i % AppFixtures::NOMBRE_DE_FAMILLES == 0 ? 2 : 1; // 1 famille sur 20 aura 2 enfants
            for ($j = 0; $j < $nbEnfants; $j++) {
                $enfant = new Enfant();
                if ($j == 0) {
                    // Premier enfant
                    $dateEntree = $famille->dateEntree;
                    $dateSortie = $famille->dateSortie;
                    $enfant->nom = $famille->nom;
                } else {
                    // Tous les autres enfants
                    $dateEntree = $faker->dateTimeBetween($famille->dateEntree, 'now');
                    $dateSortie = $faker->dateTimeBetween('now', $famille->dateSortie);
                    $enfant->nom = $faker->firstName();
                }
                $enfant->famille = $famille;
                $enfantGroupeEnfant = new EnfantGroupeEnfant();
                $enfantGroupeEnfant->enfant = $enfant;
                $enfantGroupeEnfant->groupe = $groupesEnfants[floor(sizeof($enfants) * AppFixtures::NOMBRE_DE_GROUPES_D_ENFANTS / AppFixtures::NOMBRE_D_ENFANTS)];
                $enfantGroupeEnfant->dateDebut = $dateEntree;
                $enfantGroupeEnfant->dateFin = $dateSortie;
                $manager->persist($enfantGroupeEnfant);
                $enfant->groupes[] = $enfantGroupeEnfant;

                $manager->persist($enfant);
                $enfants[] = $enfant;
            }
        }

        // Création des pros
        $pros = [];
        for ($i = 0; $i < AppFixtures::NOMBRE_DE_PROS; $i++) {
            $pro = new Pro();
            $pro->nom = $faker->firstName();
            $pro->email = $faker->email;

            $manager->persist($pro);
            $pros[] = $pro;
        }

        // Création des mois planning
        $moisPrecedent = new DateTime(date("Y-m-d", strtotime("last month")));
        for ($i = 0; $i < 12; $i++) {
            $moisPlanning = new MoisPlanning();
            $moisPlanning->code = $moisPrecedent->format('Y-m');
            $moisPlanning->dateDebut = new DateTime(date("Y-m-d", strtotime("first monday of {$moisPrecedent->format('Y-m')}")));
            $moisPlanning->dateFin = new DateTime(date("Y-m-d", strtotime("friday this week", strtotime("last monday of {$moisPrecedent->format('Y-m')}"))));
            $manager->persist($moisPlanning);

            // Création des jours planning de ce mois
            foreach (new DatePeriod($moisPlanning->dateDebut, DateInterval::createFromDateString('1 day'), $moisPlanning->dateFin->setTime(0, 0, 1)) as $date) { // let setTime permet d'inclure la date de fin, merci à https://stackoverflow.com/a/38226650/535203
                if (($date->format('w') + 6) % 7 < 5) { // le format 'w' renvoit le numéro du jour dans la semaine avec 0 pour dimanche, on décale donc de 6 jours (en modulo 7 = semaine) pour que le samedi et dimanche se retrouve à 5 et 6
                    $jourPlanning = new JourPlanning();
                    $jourPlanning->date = $date;
                    $jourPlanning->moisPlanning = $moisPlanning;
                    if ($faker->boolean(10)) { // 1 chance sur 10 d'avoir un commentaire
                        $jourPlanning->commentaire = $faker->realText();
                    }

                    $manager->persist($jourPlanning);

                    // Ajout des présences des enfants
                    foreach($enfants as $enfant) {
                        $groupe = $enfant->groupes[0];
                        if ($date->getTimestamp() >= $groupe->dateDebut->getTimestamp()
                            && $date->getTimestamp() <= $groupe->dateFin->getTimestamp()) {
                            $presenceEnfant = new PresenceEnfant();
                            $presenceEnfant->enfant = $enfant;
                            $presenceEnfant->jourPlanning = $jourPlanning;
                            // 99 chances sur 100 d'être présent
                            $presenceEnfant->present = $faker->boolean(99);
                            if (!$presenceEnfant->present) {
                                $presenceEnfant->absenceType = $this->getRandomItemOfArray(array_values(AbsenceEnfantType::values()));
                            }
                            $manager->persist($presenceEnfant);
                        }
                    }
                    // Ajout des présences des pros
                    foreach ($pros as $pro) {
                        $presencePro = new PresencePro();
                        $presencePro->pro = $pro;
                        $presencePro->jourPlanning = $jourPlanning;
                        $presencePro->present = $faker->boolean((self::NOMBRE_DE_PROS - 1) * 100 / self::NOMBRE_DE_PROS);
                        if (!$presencePro->present) {
                            if ($faker->boolean(90)) {
                                $presencePro->absenceType = AbsenceProType::OFF;
                            } else {
                                $presencePro->absenceType = $this->getRandomItemOfArray(array_values(AbsenceProType::values()));
                            }
                            if ($faker->boolean(10)) {
                                $presencePro->commentaire = $faker->realText(40);
                            }
                        } else {
                            $presencePro->heureArrivee = new DateTime($this->getRandomItemOfArray(['8:15', '8:30', '9:00', '9:30']));
                            $presencePro->heureDepart = new DateTime($this->getRandomItemOfArray(['16:30', '17:30', '18:00', '18:30']));
                            if ($faker->boolean(1)) {
                                $presencePro->commentaire = $faker->realText(40);
                            }
                        }
                        $manager->persist($presencePro);
                    }

                    // Création d'une garde d'ouverture, de 2 du matin et de 2 de l'après-midi
                    $this->creerGarde($jourPlanning, '8:00', '9:30', $familles, $manager);
                    for ($j = 0; $j < 2; $j++) {
                        $this->creerGarde($jourPlanning, '9:30', '13:30', $familles, $manager);
                    }
                    for ($j = 0; $j < 2; $j++) {
                        $this->creerGarde($jourPlanning, '13:30', '18:30', $familles, $manager);
                    }
                }
            }

            $moisPrecedent = new DateTime(date("Y-m-d", strtotime("next month", $moisPrecedent->getTimestamp())));
        }

        $manager->flush();
    }

    /**
     * @param Garde $garde
     * @param Famille[] $familles
     */
    public function assigneFamille(Garde $garde, array $familles) {
        if ($this->faker->boolean(90)) { // 9 chances sur 10
            $garde->famille = $this->getRandomItemOfArray($familles);
        }
    }

    private function creerGarde(JourPlanning $jourPlanning, string $heureArrivee, string $heureDepart, array $familles, ObjectManager $manager)
    {
        $faker = $this->faker;

        $garde = new Garde();
        $garde->jourPlanning = $jourPlanning;
        $garde->heureArrivee = new DateTime($heureArrivee);
        $garde->heureDepart = new DateTime($heureDepart);
        // assignation d'une famille aléatoirement
        if ($faker->boolean(90)) {
            $garde->famille = $this->getRandomItemOfArray($familles);
        }
        // commentaire aléatoire
        if ($faker->boolean(10)) { // 1 chances sur 10
            $garde->commentaire = $faker->realText(40);
        }
        // familles disponibles
        $nbFamillesDisponibles = $faker->biasedNumberBetween(0, AppFixtures::NOMBRE_DE_FAMILLES, function($x) { return 1 - $x; });
        for ($i = 0; $i < $nbFamillesDisponibles; $i++) {
            $garde->addFamilleDisponible($this->getRandomItemOfArray($familles));
        }
        $manager->persist($garde);
    }

    /**
     * @template T
     * @param T[] $array
     * @return T
     */
    private function getRandomItemOfArray(array $array)
    {
        return $array[$this->faker->numberBetween(0, sizeof($array) - 1)];
    }
}
