<?php


namespace App\Repository;


use App\Entity\Contrat;
use App\Entity\Famille;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ContratRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contrat::class);
    }

    /**
     * @return Contrat[]
     */
    public function findByFamilleAndDate(Famille $famille, DateTime $date): array {
        return $this->createQueryBuilder('contrat')
            ->select('contrat')
            ->join('contrat.enfant', 'enfant')
            ->join('enfant.famille', 'famille')
            ->where('famille = :famille AND :date BETWEEN contrat.dateDebut AND contrat.dateFin')
            ->setParameter('famille', $famille)
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();
    }
}