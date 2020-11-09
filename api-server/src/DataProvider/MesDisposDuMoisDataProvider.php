<?php


namespace App\DataProvider;


use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\NoDb\GardeDisponible;
use App\Entity\NoDb\MesDisposDuMois;
use App\Repository\CommentaireFamilleMoisPlanningRepository;
use App\Repository\GardeRepository;
use App\Repository\MoisPlanningRepository;
use Doctrine\ORM\NonUniqueResultException;

class MesDisposDuMoisDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    private $gardeRepository;
    private $moisPlanningRepository;
    private $commentaireFamilleMoisPlanningRepository;

    public function __construct(
        GardeRepository $gardeRepository,
        MoisPlanningRepository $moisPlanningRepository,
        CommentaireFamilleMoisPlanningRepository $commentaireFamilleMoisPlanningRepository
    )
    {
        $this->gardeRepository = $gardeRepository;
        $this->moisPlanningRepository = $moisPlanningRepository;
        $this->commentaireFamilleMoisPlanningRepository = $commentaireFamilleMoisPlanningRepository;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return MesDisposDuMois::class === $resourceClass;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        $explodedId = explode("_", $id);
        $codeMoisPlanning = $explodedId[0];
        $idFamille = $explodedId[1];

        if ($operationName == "get") {
            return $this->getMesDisposDuMois($codeMoisPlanning, $idFamille);
        } else {
            return new MesDisposDuMois();
        }
    }

    /**
     * @param string $codeMoisPlanning
     * @param string $idFamille
     * @return MesDisposDuMois
     * @throws NonUniqueResultException
     */
    public function getMesDisposDuMois(string $codeMoisPlanning, string $idFamille): MesDisposDuMois
    {
        $mesDisposDuMois = new MesDisposDuMois();
        $mesDisposDuMois->code = $codeMoisPlanning . "_" . $idFamille;

        // Ajout du mois
        $mesDisposDuMois->moisPlanning = $this->moisPlanningRepository->findOneByCode($codeMoisPlanning);

        // Ajout du commentaire de la famille
        $mesDisposDuMois->commentaireFamilleMoisPlanning = $this->commentaireFamilleMoisPlanningRepository->findOneByMoisPlanningIdAndFamilleId($mesDisposDuMois->moisPlanning->getId(), $idFamille);

        // Ajout des gardes du mois
        $gardes = $this->gardeRepository->findByJourPlanningMoisPlanningCode($codeMoisPlanning);
        foreach ($gardes as $garde) {
            $gardeDisponible = new GardeDisponible();
            $gardeDisponible->garde = $garde;
            // Ajout de la disponibilité de la famille sur cette garde
            $gardeDisponible->familleDisponible = $this->gardeRepository->isFamilleDisponibleByGardeIdAndFamilleId($garde->getId(), $idFamille);
            $mesDisposDuMois->gardesDisponibles[] = $gardeDisponible;
        }

        return $mesDisposDuMois;
    }

}