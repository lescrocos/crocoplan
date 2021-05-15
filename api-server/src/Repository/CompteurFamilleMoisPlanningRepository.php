<?php


namespace App\Repository;


use App\Entity\CompteurFamilleMoisPlanning;
use App\Entity\Famille;
use App\Entity\MoisPlanning;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

class CompteurFamilleMoisPlanningRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompteurFamilleMoisPlanning::class);
    }


    /**
     * @throws NonUniqueResultException
     */
    public function findByFamilleAndMoisPlanning(Famille $famille, MoisPlanning $moisPlanning): ?CompteurFamilleMoisPlanning
    {
        return $this->createQueryBuilder('compteurFamilleMoisPlanning')
            ->select('compteurFamilleMoisPlanning')
            ->where('compteurFamilleMoisPlanning.famille = :famille AND compteurFamilleMoisPlanning.moisPlanning = :moisPlanning')
            ->setParameter('famille', $famille)
            ->setParameter('moisPlanning', $moisPlanning)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param MoisPlanning $moisPlanning
     * @param Famille[] $familles
     */
    public function deleteByMoisPlanningAndFamilleNotIn(MoisPlanning $moisPlanning, array $familles)
    {
        return $this->createQueryBuilder('compteurFamilleMoisPlanning')
            ->delete('App\Entity\CompteurFamilleMoisPlanning', 'compteurFamilleMoisPlanning')
            ->where('compteurFamilleMoisPlanning.moisPlanning = :moisPlanning AND compteurFamilleMoisPlanning.famille NOT IN (:familles)')
            ->setParameter('moisPlanning', $moisPlanning)
            ->setParameter('familles', $familles)
            ->getQuery()
            ->execute();
    }
}