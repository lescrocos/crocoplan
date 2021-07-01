<?php


namespace App\DataPersister;


use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\DataProvider\MesDisposDuMoisDataProvider;
use App\Entity\CParent;
use App\Entity\NoDb\MesDisposDuMois;
use App\Entity\Utilisateur;
use App\Repository\GardeRepository;
use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Security;

class MesDisposDuMoisDataPersister implements ContextAwareDataPersisterInterface
{
    private $gardeRepository;
    private $mesDisposDuMoisDataProvider;
    private $security;

    public function __construct(
        GardeRepository $gardeRepository,
        MesDisposDuMoisDataProvider $mesDisposDuMoisDataProvider,
        Security $security
    )
    {
        $this->gardeRepository = $gardeRepository;
        $this->mesDisposDuMoisDataProvider = $mesDisposDuMoisDataProvider;
        $this->security = $security;
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
        $user = $this->security->getUser();
        if (empty($user)) {
            throw new UnauthorizedHttpException();
        } else if (!$user instanceof CParent) {
            throw new AccessDeniedHttpException();
        }

        $mesDisposDuMois = $data;

        $codeMoisPlanning = $mesDisposDuMois->code;
        $idFamille = $user->famille->getId();

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