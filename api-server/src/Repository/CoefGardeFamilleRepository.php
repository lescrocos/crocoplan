<?php


namespace App\Repository;


use App\Entity\CoefGardeFamille;
use App\Entity\Famille;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CoefGardeFamilleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CoefGardeFamille::class);
    }


    /**
     * @return CoefGardeFamille[]
     */
    public function findByFamilleAndDateOrderByCoef(Famille $famille, DateTime $date): array
    {
        return $this->createQueryBuilder('coefGardeFamille')
            ->select('coefGardeFamille')
            ->where(':date BETWEEN coefGardeFamille.dateDebut AND coefGardeFamille.dateFin')
            ->orderBy('coefGardeFamille.coef', 'ASC')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();
    }
}