<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Un mois de planning
 * @package App\Entity
 * @ORM\Entity
 * @ApiResource
 */
class MoisPlanning
{
    /**
     * @var int ID du jour de planning
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var DateTime La date du premier lundi de ce mois planning
     *
     * @ORM\Column(type="date")
     * @Assert\NotNull
     */
    public $dateDebut;

    /**
     * @var DateTime La date du dernier dimanche de ce mois planning
     *
     * @ORM\Column(type="date")
     * @Assert\NotNull
     */
    public $dateFin;

    /**
     * @var string Commentaire de ce mois de planning
     *
     * @ORM\Column(nullable=true)
     */
    public $commentaire;

    /**
     * @var CommentaireFamilleMoisPlanning[] Les présences enfants
     *
     * @ORM\OneToMany(targetEntity="JourPlanning", mappedBy="moisPlanning")
     */
    public $joursPlanning;


    public function __construct()
    {
        $this->joursPlanning = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

}
