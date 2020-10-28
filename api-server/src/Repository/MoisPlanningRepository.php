<?php


namespace App\Repository;


use App\Entity\MoisPlanning;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

class MoisPlanningRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MoisPlanning::class);
    }

    /**
     * @param string $code
     * @return MoisPlanning|null
     * @throws NonUniqueResultException
     */
    public function findOneByCode(string $code)
    {
        return $this->getEntityManager()->createQuery('
            SELECT m FROM App\Entity\MoisPlanning m
            WHERE m.code = :code
        ')
            ->setParameter('code', $code)
            ->getOneOrNullResult();
    }

}