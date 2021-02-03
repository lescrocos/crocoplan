<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Un enfant
 * @package App\Entity
 * @ORM\Entity
 * @ApiResource(
 *     collectionOperations={"get" = {"normalization_context" = {"groups" = "enfant"}}}
 * )
 */
class Enfant
{
    /**
     * @var int ID de l'enfant
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @Groups({"jour_planning", "enfant"})
     */
    private $id;

    /**
     * @var string Le nom de l'enfant
     *
     * @ORM\Column
     * @Assert\NotBlank
     *
     * @Groups({"enfant"})
     */
    public $nom;

    /**
     * @var DateTime La date d'entrée de l'enfant à la crèche
     *
     * @ORM\Column(type="date")
     * @Assert\NotNull
     *
     * @Groups({"enfant"})
     */
    public $dateEntree;

    /**
     * @var DateTime La date de sortie de l'enfant de la crèche
     *
     * @ORM\Column(type="date")
     * @Assert\NotNull
     *
     * @Groups({"enfant"})
     */
    public $dateSortie;

    /**
     * @var DateTime La date de début d'adaptation de cet enfant
     *
     * @ORM\Column(type="date", nullable=true)
     *
     * @Groups({"enfant"})
     */
    public $debutAdaptation;

    /**
     * @var DateTime La date de fin d'adaptation de cet enfant
     *
     * @ORM\Column(type="date", nullable=true)
     *
     * @Groups({"enfant"})
     */
    public $finAdaptation;

    /**
     * @var Famille La famille de l'enfant
     *
     * @ORM\ManyToOne(targetEntity="Famille", inversedBy="enfants")
     * @ORM\JoinColumn(nullable=false)
     */
    public $famille;

    /**
     * @var EnfantGroupeEnfant[] Les association à des groupes d'enfants de cet enfant
     *
     * @ORM\OneToMany(targetEntity=EnfantGroupeEnfant::class, mappedBy="enfant")
     *
     * @Groups({"enfant"})
     */
    public $groupes;


    public function __construct()
    {
        $this->groupes = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

}
