<?php


namespace App\Repository;


use App\Entity\Garde;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

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

    /**
     * @param int $idGarde
     * @param int $idFamille
     * @return bool
     * @throws DBALException
     */
    public function isFamilleDisponibleByGardeIdAndFamilleId(int $idGarde, int $idFamille): bool
    {
        $result = $this->getEntityManager()->getConnection()
            ->executeQuery(
                'SELECT 1 FROM garde_famille_disponible
                 WHERE garde_id = :idGarde AND famille_id = :idFamille',
                ["idGarde" => $idGarde, "idFamille" => $idFamille]
            )
            ->fetch();
        return is_array($result) && sizeof($result) == 1;

        // Version + lente en DQL
//        $result = $this->getEntityManager()->createQuery('
//            SELECT 1 FROM App\Entity\Garde g, App\Entity\Famille f
//            WHERE g.id = :idGarde AND f.id = :idFamille AND f MEMBER OF g.famillesDisponibles
//        ')
//            ->setParameter('idGarde', $idGarde)
//            ->setParameter('idFamille', $idFamille)
//            ->getResult();
//        return sizeof($result) == 1;

        // Version qui ne fonctionne pas
//        $result = $this->getEntityManager()->createNativeQuery('
//            SELECT 1 FROM garde_famille_disponible
//            WHERE garde_id = :idGarde AND famille_id = :idFamille
//        ', new ResultSetMapping())
//            ->setParameter('idGarde', $idGarde)
//            ->setParameter('idFamille', $idFamille)
//            ->getResult();
//        return sizeof($result) == 1;
    }

    /**
     * @param int $idFamille
     * @param array $idsGardes
     * @param string $codeMoisPlanning
     * @throws ConnectionException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws DBALException
     */
    public function setFamilleDisponibleByIdFamilleAndIdsGardesAndCodeMoisPlanning(int $idFamille, array $idsGardes, string $codeMoisPlanning) {
        $em = $this->getEntityManager();
        // On commence une transaction en suivant la documentation https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/transactions-and-concurrency.html#approach-2-explicitly
        $em->getConnection()->beginTransaction();
        try {
            // Suppression de toutes les disonibilités de garde pour la famille concernée pour le mois donné
            $params["idFamille"] = $idFamille;
            $params["codeMoisPlanning"] = $codeMoisPlanning;
            $em->getConnection()->executeUpdate('
                DELETE FROM garde_famille_disponible
                WHERE famille_id = :idFamille
                  AND garde_id IN (
                    SELECT g.id
                    FROM garde g
                      JOIN jour_planning j ON g.jour_planning_id = j.id
                      JOIN mois_planning m ON j.mois_planning_id = m.id
                    WHERE m.code = :codeMoisPlanning
                  )
            ', $params);

//            $em->createNativeQuery('
//                DELETE FROM garde_famille_disponible
//                WHERE famille_id = :idFamille
//                  AND garde_id IN (
//                    SELECT g.id
//                    FROM garde g
//                      JOIN jour_planning j ON g.jour_planning_id = j.id
//                      JOIN mois_planning m ON j.mois_planning_id = m.id
//                    WHERE m.code = :codeMoisPlanning
//                  )
//            ', new ResultSetMapping())
//                ->setParameter('idFamille', $idFamille)
//                ->setParameter('codeMoisPlanning', $codeMoisPlanning)
//                ->execute();

            // Puis ajout des disponibilités de gardes passées en paramètre
            $statement = $em->getConnection()->prepare('
                    INSERT INTO garde_famille_disponible(famille_id, garde_id)
                    SELECT :idFamille, :idGarde FROM dual
                    WHERE EXISTS(SELECT 1
                      FROM garde g
                        JOIN jour_planning j ON g.jour_planning_id = j.id
                        JOIN mois_planning m ON j.mois_planning_id = m.id
                      WHERE m.code = :codeMoisPlanning AND g.id = :idGarde
                    )
                ');
            foreach ($idsGardes as $idGarde) {
                $params['idGarde'] = $idGarde;
                $statement->execute($params);

//                $em->createNativeQuery('
//                    IF EXISTS(SELECT 1
//                      FROM garde g
//                        JOIN jour_planning j ON g.jour_planning_id = j.id
//                        JOIN mois_planning m ON j.mois_planning_id = m.id
//                      WHERE m.code = :codeMoisPlanning AND g.id = :idGarde
//                    )
//                    INSERT INTO garde_famille_disponible(famille_id, garde_id)
//                    VALUES (:idFamille, :idGarde)
//                ', new ResultSetMapping())
//                    ->setParameter('idFamille', $idFamille)
//                    ->setParameter('idGarde', $idGarde)
//                    ->setParameter('codeMoisPlanning', $codeMoisPlanning)
//                    ->execute();
            }
            $em->flush();
            $em->getConnection()->commit();
        } catch (Exception $e) {
            $em->getConnection()->rollBack();
            throw $e;
        }
    }

}