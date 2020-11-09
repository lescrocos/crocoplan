<?php


namespace App\DataPersister;


use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\DataProvider\MesDisposDuMoisDataProvider;
use App\Entity\NoDb\MesDisposDuMois;
use App\Repository\GardeRepository;
use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

class MesDisposDuMoisDataPersister implements ContextAwareDataPersisterInterface
{
    private $gardeRepository;
    private $mesDisposDuMoisDataProvider;

    public function __construct(
        GardeRepository $gardeRepository,
        MesDisposDuMoisDataProvider $mesDisposDuMoisDataProvider
    )
    {
        $this->gardeRepository = $gardeRepository;
        $this->mesDisposDuMoisDataProvider = $mesDisposDuMoisDataProvider;
    }


    public function supports($data, array $context = []): bool
    {
        return $data instanceof MesDisposDuMois;
    }

    /**
     * @param MesDisposDuMois $data
     * @param array $context
     * @return object|void
     * @throws ConnectionException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws DBALException
     */
    public function persist($data, array $context = [])
    {
        $mesDisposDuMois = $data;

        $explodedId = explode("_", $mesDisposDuMois->code);
        $codeMoisPlanning = $explodedId[0];
        $idFamille = $explodedId[1];

        $this->gardeRepository->setFamilleDisponibleByIdFamilleAndIdsGardesAndCodeMoisPlanning(
          $idFamille,
          $mesDisposDuMois->gardesDisponiblesIds,
          $codeMoisPlanning
        );

        return $this->mesDisposDuMoisDataProvider->getMesDisposDuMois($codeMoisPlanning, $idFamille);
    }

    public function remove($data, array $context = [])
    {
        throw new \LogicException("Not implemented");
    }
}