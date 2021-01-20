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
     * @var integer Compteur calculé (en nombres de secondes) de gardes restant à réaliser (si négatif) ou "stocké" (si positif) à la fin de ce mois de planning
     * @ORM\Column
     */
    public $compteur;

    /**
     * @var integer Compteur forcé (en nombres de secondes) de gardes restant à réaliser (si négatif) ou "stocké" (si positif) à la fin de ce mois de planning. Prend dans ce cas le pas sur le compteur calculé lors du mois suivant.
     * @ORM\Column
     */
    public $compteurForce;

    /**
     * @var MoisPlanning le mois planning de ce jour
     *
     * @ORM\ManyToOne(targetEntity="MoisPlanning")
     * @ORM\JoinColumn(nullable=false)
     */
    public $moisPlanning;

    /**
     * @var Famille la famille qui dépose ce commentaire
     *
     * @ORM\ManyToOne(targetEntity="Famille")
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
