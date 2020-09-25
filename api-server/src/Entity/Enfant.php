<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Un enfant
 * @package App\Entity
 * @ORM\Entity
 * @ApiResource
 */
class Enfant
{
    /**
     * @var int ID de l'enfant
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string Le nom de l'enfant
     *
     * @ORM\Column
     * @Assert\NotBlank
     */
    public $nom;

    /**
     * @var DateTime La date d'entrée de l'enfant à la crèche
     *
     * @ORM\Column(type="date")
     * @Assert\NotNull
     */
    public $dateEntree;

    /**
     * @var DateTime La date de sortie de l'enfant de la crèche
     *
     * @ORM\Column(type="date")
     * @Assert\NotNull
     */
    public $dateSortie;

    /**
     * @var DateTime La date de début d'adaptation de cet enfant
     *
     * @ORM\Column(type="date", nullable=true)
     */
    public $debutAdaptation;

    /**
     * @var DateTime La date de fin d'adaptation de cet enfant
     *
     * @ORM\Column(type="date", nullable=true)
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
     * @var PresenceEnfant[] Les présences / absences de cet enfant
     *
     * @ORM\OneToMany(targetEntity="PresenceEnfant", mappedBy="enfant")
     */
    public $presences;


    public function __construct()
    {
        $this->presences = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

}
