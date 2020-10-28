<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

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
     *
     * @Groups({"garde", "mes_dispos_du_mois"})
     */
    public $date;

    /**
     * @var string Commentaire de ce jour de planning
     *
     * @ORM\Column(nullable=true)
     */
    public $commentaire;

    /**
     * @var MoisPlanning le mois planning de ce jour
     *
     * @ORM\ManyToOne(targetEntity="MoisPlanning", inversedBy="joursPlanning")
     * @ORM\JoinColumn(nullable=false)
     */
    public $moisPlanning;

    /**
     * @var PresenceEnfant[] Les présences enfants
     *
     * @ORM\OneToMany(targetEntity="PresenceEnfant", mappedBy="jourPlanning")
     */
    public $presencesEnfants;

    /**
     * @var PresencePro[] Les présences pros
     *
     * @ORM\OneToMany(targetEntity="PresencePro", mappedBy="jourPlanning")
     */
    public $presencesPros;

    /**
     * @var Garde[] Les gardes parent
     *
     * @ORM\OneToMany(targetEntity="Garde", mappedBy="jourPlanning")
     */
    public $gardes;


    public function __construct()
    {
        $this->presencesEnfants = new ArrayCollection();
        $this->presencesPros = new ArrayCollection();
        $this->gardes = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

}
