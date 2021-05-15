<?php


namespace App\Repository;


use App\Entity\JourPlanning;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class JourPlanningRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JourPlanning::class);
    }


    /**
     * @param string $codeMoisPlanning
     * @return JourPlanning[]
     */
    public function findByMoisPlanningCode(string $codeMoisPlanning): array
    {
        return $this->createQueryBuilder('jourPlanning')
            ->join('jourPlanning.moisPlanning', 'moisPlanning')
            ->where('moisPlanning.code = :codeMoisPlanning')
            ->setParameter('codeMoisPlanning', $codeMoisPlanning)
            ->getQuery()
            ->getResult();
    }

}