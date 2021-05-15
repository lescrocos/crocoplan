<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Représente le compteur calculé de garde d'une famille sur un mois de planning
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(columns={"mois_planning_id", "famille_id"})})
 */
class CompteurFamilleMoisPlanning
{
    /**
     * @var int ID
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var integer Compteur calculé (en nombre de secondes) de gardes restant à réaliser (si négatif) ou "stocké" (si positif) à la fin de ce mois de planning
     * @ORM\Column(type="integer")
     */
    public $compteur;

    /**
     * @var integer|null Compteur forcé (en nombre de secondes) de gardes restant à réaliser (si négatif) ou "stocké" (si positif) à la fin de ce mois de planning. Prend dans ce cas le pas sur le compteur calculé lors du mois suivant.
     * @ORM\Column(type="integer", nullable=true)
     */
    public $compteurForce;

    /**
     * @var integer Quota de jours d'absence (en nombre de jour) restant à la fin de ce mois de planning.
     * @ORM\Column(type="integer")
     */
    public $quotaJoursAbsence;

    /**
     * @var integer|null Quota de jours d'absence forcé (en nombre de jour) restant à la fin de ce mois de planning. Prend dans ce cas le pas sur le quota de jours d'absence lors du mois suivant.
     * @ORM\Column(type="integer", nullable=true)
     */
    public $quotaJoursAbsenceForce;

    /**
     * @var float Coefficient total pour le mois de cette famille, avant la division par le nombre de jours de gardes du mois
     * @ORM\Column(type="float")
     */
    public $coefTotal;

    /**
     * @var float Coefficient pour le mois de cette famille après division par le nombre de jours de gardes du mois
     * @ORM\Column(type="float")
     */
    public $coef;

    /**
     * @var integer Nombre de secondes de gardes effectuées par la famille sur ce mois de planning
     * @ORM\Column(type="integer")
     */
    public $tempsGardesTotal;

    /**
     * @var integer Nombre de secondes de gardes à réaliser par la famille sur ce mois de planning
     * @ORM\Column(type="integer")
     */
    public $tempsARealiser;

    /**
     * @var integer Évolution du compteur de cette famille pour ce mois de planning
     * @ORM\Column(type="integer")
     */
    public $evolutionCompteur;

    /**
     * @var integer Nombre de secondes de gardes à réaliser par la famille sur ce mois de planning (en tenant compte des gardes non attribuées)
     * @ORM\Column(type="integer")
     */
    public $tempsARealiserNA;

    /**
     * @var integer Évolution du compteur de cette famille pour ce mois de planning (en tenant compte des gardes non attribuées)
     * @ORM\Column(type="integer")
     */
    public $evolutionCompteurNA;

    /**
     * @var integer Compteur calculé (en nombre de secondes) de gardes restant à réaliser (si négatif) ou "stocké" (si positif) à la fin de ce mois de planning (en tenant compte des gardes non attribuées)
     * @ORM\Column(type="integer")
     */
    public $compteurNA;


    /**
     * @var MoisPlanning le mois planning de ce jour
     *
     * @ORM\ManyToOne(targetEntity=MoisPlanning::class)
     * @ORM\JoinColumn(nullable=false)
     */
    public $moisPlanning;

    /**
     * @var Famille la famille qui dépose ce commentaire
     *
     * @ORM\ManyToOne(targetEntity=Famille::class)
     * @ORM\JoinColumn(nullable=false)
     */
    public $famille;

    /**
     * @var DateTime La version cette présence / absence (permet "l'optimistic-locking" https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/reference/transactions-and-concurrency.html#optimistic-locking )
     *
     * @ORM\Version @ORM\Column(type="datetime", nullable=false)
     */
    public $version;


    public function getId(): ?int
    {
        return $this->id;
    }

}
