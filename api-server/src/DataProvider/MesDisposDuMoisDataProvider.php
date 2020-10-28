<?php


namespace App\DataProvider;


use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\NoDb\GardeDisponible;
use App\Entity\NoDb\MesDisposDuMois;
use App\Repository\CommentaireFamilleMoisPlanningRepository;
use App\Repository\GardeRepository;
use App\Repository\MoisPlanningRepository;

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
        $mesDisposDuMois = new MesDisposDuMois();
        $mesDisposDuMois->code = $id;

        $explodedId = explode("_", $id);
        $codeMoisPlanning = $explodedId[0];
        $idFamille = $explodedId[1];

        $mesDisposDuMois->moisPlanning = $this->moisPlanningRepository->findOneByCode($codeMoisPlanning);

        $mesDisposDuMois->commentaireFamilleMoisPlanning = $this->commentaireFamilleMoisPlanningRepository->findOneByMoisPlanningIdAndFamilleId($mesDisposDuMois->moisPlanning->getId(), $idFamille);

        $gardes = $this->gardeRepository->findByJourPlanningMoisPlanningCode($codeMoisPlanning);
        foreach ($gardes as $garde) {
            $gardeDisponible = new GardeDisponible();
            $gardeDisponible->garde = $garde;
            $gardeDisponible->familleDisponible = $this->gardeRepository->isFamilleDisponibleByGardeIdAndFamilleId($garde->getId(), $idFamille);
            $mesDisposDuMois->gardesDisponibles[] = $gardeDisponible;
        }

        return $mesDisposDuMois;
    }

}