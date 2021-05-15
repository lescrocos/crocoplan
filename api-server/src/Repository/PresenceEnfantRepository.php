<?php


namespace App\Repository;


use App\Entity\Enfant;
use App\Entity\JourPlanning;
use App\Entity\PresenceEnfant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

class PresenceEnfantRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PresenceEnfant::class);
    }


    /**
     * @throws NonUniqueResultException
     */
    public function findOneByJourPlanningAndEnfant(JourPlanning $jourPlanning, Enfant $enfant): ?PresenceEnfant {
        return $this->createQueryBuilder('presenceEnfant')
            ->where('presenceEnfant.jourPlanning = :jourPlanning AND presenceEnfant.enfant = :enfant')
            ->setParameter('jourPlanning', $jourPlanning)
            ->setParameter('enfant', $enfant)
            ->getQuery()
            ->getOneOrNullResult();
    }
}