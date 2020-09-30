<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Représente le commentaire que peut laisser une famille à destination de la famille Planning 1 pour réaliser le planning du mois
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(columns={"mois_planning_id", "famille_id"})})
 * @ApiResource
 */
class CommentaireFamilleMoisPlanning
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
     * @var string Instructions
     *
     * @ORM\Column
     */
    public $commentaire;

    /**
     * @var CoefFamille le mois planning de ce jour
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


    public function getId(): ?int
    {
        return $this->id;
    }

}