<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Une famille
 * @package App\Entity
 * @ORM\Entity
 * @ApiResource
 */
class Famille
{
    /**
     * @var int ID de la famille
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string Le nom de la famille
     *
     * @ORM\Column
     * @Assert\NotBlank
     */
    public $nom;

    /**
     * @var DateTime La date d'entrée de la famille à la crèche
     *
     * @ORM\Column(type="date")
     * @Assert\NotNull
     */
    public $dateEntree;

    /**
     * @var DateTime La date de sortie de la famille de la crèche
     *
     * @ORM\Column(type="date")
     * @Assert\NotNull
     */
    public $dateSortie;

    /**
     * @var Enfant[] Les enfants de la famille
     *
     * @ORM\OneToMany(targetEntity="Enfant", mappedBy="famille")
     */
    public $enfants;

    /**
     * @var CParent[] Les parents de la famille
     *
     * @ORM\OneToMany(targetEntity="CParent", mappedBy="famille")
     */
    public $parents;

    /**
     * @var Garde[] Les gardes affectées à cette famille
     *
     * @ORM\OneToMany(targetEntity="Garde", mappedBy="famille")
     */
    public $gardes;

    /**
     * @var Garde[] Les gardes disponibles de la famille
     *
     * @ORM\ManyToMany(targetEntity="Garde", inversedBy="famillesDisponibles")
     * @ORM\JoinTable(name="garde_famille_disponible")
     */
    public $gardesDisponibles;


    public function __construct()
    {
        $this->enfants = new ArrayCollection();
        $this->parents = new ArrayCollection();
        $this->gardes = new ArrayCollection();
        $this->gardesDisponibles = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

}