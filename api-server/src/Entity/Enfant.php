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
     * @var DateTime|null La date de début d'adaptation de cet enfant
     *
     * @ORM\Column(type="date", nullable=true)
     *
     * @Groups({"enfant"})
     */
    public $debutAdaptation;

    /**
     * @var DateTime|null La date de fin d'adaptation de cet enfant
     *
     * @ORM\Column(type="date", nullable=true)
     *
     * @Groups({"enfant"})
     */
    public $finAdaptation;

    /**
     * @var Famille La famille de l'enfant
     *
     * @ORM\ManyToOne(targetEntity=Famille::class, inversedBy="enfants")
     * @ORM\JoinColumn(nullable=false)
     */
    public $famille;

    /**
     * @var Contrat[] Les contrats de cet enfant
     *
     * @ORM\OneToMany(targetEntity=Contrat::class, mappedBy="enfant")
     *
     * @Groups({"enfant"})
     */
    public $contrats;


    public function __construct()
    {
        $this->contrats = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

}
