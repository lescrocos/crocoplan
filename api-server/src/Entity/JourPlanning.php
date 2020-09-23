<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Un jour de planning
 * @package App\Entity
 * @ORM\Entity
 * @ApiResource
 */
class JourPlanning
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
     * @var DateTime La date de ce jour de planning
     *
     * @ORM\Column(type="date")
     * @Assert\NotNull
     */
    public $date;

    /**
     * @var string Commentaire de ce jour de planning
     *
     * @ORM\Column(nullable=true)
     */
    public $commentaire;


    /**
     * @var PresenceJourPlanning[] Les présences pros / enfants
     *
     * @ORM\OneToMany(targetEntity="PresenceJourPlanning", mappedBy="jourPlanning")
     */
    public $presencesJourPlanning;

    /**
     * @var Garde[] Les gardes parent
     *
     * @ORM\OneToMany(targetEntity="Garde", mappedBy="jourPlanning")
     */
    public $gardes;


    public function __construct()
    {
        $this->presencesJourPlanning = new ArrayCollection();
        $this->gardes = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

}
