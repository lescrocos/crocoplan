<?php


namespace App\Repository;


use App\Entity\Garde;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

class GardeRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Garde::class);
    }

    /**
     * @param string $codeMoisPlanning
     * @return Garde[]
     */
    public function findByJourPlanningMoisPlanningCode(string $codeMoisPlanning): array
    {
        return $this->getEntityManager()->createQuery('
            SELECT g FROM App\Entity\Garde g JOIN g.jourPlanning j JOIN j.moisPlanning m
            WHERE m.code = :codeMoisPlanning
        ')
            ->setParameter('codeMoisPlanning', $codeMoisPlanning)
            ->getResult();
    }

    public function isFamilleDisponibleByGardeIdAndFamilleId(int $idGarde, int $idFamille): bool
    {
        $result = $this->getEntityManager()->createQuery('
            SELECT 1 FROM App\Entity\Garde g, App\Entity\Famille f
            WHERE g.id = :idGarde AND f.id = :idFamille AND f MEMBER OF g.famillesDisponibles
        ')
            ->setParameter('idGarde', $idGarde)
            ->setParameter('idFamille', $idFamille)
            ->getResult();
        return sizeof($result) == 1;

//        $result = $this->getEntityManager()->createNativeQuery('
//            SELECT 1 FROM garde_famille_disponible
//            WHERE garde_id = :idGarde AND famille_id = :idFamille
//        ', new ResultSetMapping())
//            ->setParameter('idGarde', $idGarde)
//            ->setParameter('idFamille', $idFamille)
//            ->getResult();
//        return sizeof($result) == 1;
    }

}