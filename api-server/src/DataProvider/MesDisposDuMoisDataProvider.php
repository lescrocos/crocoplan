<?php


namespace App\DataProvider;


use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\CParent;
use App\Entity\NoDb\MesDisposDuMois;
use App\Repository\CommentaireFamilleMoisPlanningRepository;
use App\Repository\GardeRepository;
use App\Repository\MoisPlanningRepository;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Security;

class MesDisposDuMoisDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    private $gardeRepository;
    private $moisPlanningRepository;
    private $commentaireFamilleMoisPlanningRepository;
    private $security;

    public function __construct(
        GardeRepository $gardeRepository,
        MoisPlanningRepository $moisPlanningRepository,
        CommentaireFamilleMoisPlanningRepository $commentaireFamilleMoisPlanningRepository,
        Security $security
    )
    {
        $this->gardeRepository = $gardeRepository;
        $this->moisPlanningRepository = $moisPlanningRepository;
        $this->commentaireFamilleMoisPlanningRepository = $commentaireFamilleMoisPlanningRepository;
        $this->security = $security;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return MesDisposDuMois::class === $resourceClass;
    }

    /**
     * @throws NonUniqueResultException
     * @throws DBALException
     */
    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        $user = $this->security->getUser();
        if (empty($user)) {
            throw new UnauthorizedHttpException();
        } else if (!$user instanceof CParent) {
            throw new AccessDeniedHttpException();
        }

        $codeMoisPlanning = $id;
        $idFamille = $user->famille->getId();

        if ($operationName == "get") {
            return $this->getMesDisposDuMois($codeMoisPlanning, $idFamille);
        } else {
            // Inutile de faire des requêtes pour rien lorsque l'on sauvegarde : on ne va donc pas faire appel à la méthode de récupération des dispos du mois
            return new MesDisposDuMois();
        }
    }

    /**
     * @param string $codeMoisPlanning
     * @param string $idFamille
     * @return MesDisposDuMois
     * @throws NonUniqueResultException
     * @throws DBALException
     */
    public function getMesDisposDuMois(string $codeMoisPlanning, string $idFamille): MesDisposDuMois
    {
        $mesDisposDuMois = new MesDisposDuMois();
        $mesDisposDuMois->code = $codeMoisPlanning;

        // Ajout du mois
        $mesDisposDuMois->moisPlanning = $this->moisPlanningRepository->findOneByCode($codeMoisPlanning);

        // Ajout du commentaire de la famille
        $mesDisposDuMois->commentaireFamilleMoisPlanning = $this->commentaireFamilleMoisPlanningRepository->findOneByMoisPlanningIdAndFamilleId($mesDisposDuMois->moisPlanning->getId(), $idFamille);

        // Ajout des gardes du mois
        $mesDisposDuMois->gardes = $this->gardeRepository->findByJourPlanningMoisPlanningCode($codeMoisPlanning);
        $mesDisposDuMois->gardesDisponiblesIds = $this->gardeRepository->getIdsGardesDisponiblesByIdFamilleAndIdsGardesAndCodeMoisPlanning($idFamille, $codeMoisPlanning);

        return $mesDisposDuMois;
    }

}