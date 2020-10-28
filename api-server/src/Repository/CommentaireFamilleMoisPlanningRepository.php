<?php


namespace App\Repository;


use App\Entity\CommentaireFamilleMoisPlanning;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

class CommentaireFamilleMoisPlanningRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommentaireFamilleMoisPlanning::class);
    }

    /**
     * @param int $idMoisPlanning
     * @param int $idFamille
     * @return CommentaireFamilleMoisPlanning|null
     * @throws NonUniqueResultException
     */
    public function findOneByMoisPlanningIdAndFamilleId(int $idMoisPlanning, int $idFamille)
    {
        return $this->getEntityManager()->createQuery('
            SELECT m FROM App\Entity\CommentaireFamilleMoisPlanning m
            WHERE m.moisPlanning = :idMoisPlanning
              AND m.famille = :idFamille
        ')
            ->setParameter('idMoisPlanning', $idMoisPlanning)
            ->setParameter('idFamille', $idFamille)
            ->getOneOrNullResult();
    }

}