<?php


namespace App\Repository;


use App\Entity\Famille;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FamilleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Famille::class);
    }


    /**
     * @return Famille[]
     */
    public function findBetweenDateDebutAndDateFin(DateTime $dateDebut, DateTime $dateFin): array
    {
        return $this->createQueryBuilder('famille')
            ->select('famille')
            ->join('famille.enfants', 'enfant')
            ->join('enfant.contrats', 'contrat')
            ->where('contrat.dateDebut < :dateFin AND contrat.dateFin > :dateDebut')
            ->setParameter('dateDebut', $dateDebut)
            ->setParameter('dateFin', $dateFin)
            ->getQuery()
            ->getResult();
    }
}