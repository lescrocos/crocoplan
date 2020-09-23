<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;
use App\Enum\PresenceEnfantType;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Représente la présence ou l'absence d'un enfant pour un jour donné
 * @package App\Entity
 * @ORM\Entity
 * @ApiResource
 */
class PresenceEnfant
{
    /**
     * @var int ID de cette présence / absence d'enfant
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var DateTime La date de cette présence / absence d'enfant
     *
     * @ORM\Column(type="date")
     * @Assert\NotNull
     */
    public $date;

    /**
     * @var string Type de présence de l'enfant
     *
     * @ORM\Column
     * @Assert\NotNull
     * @Assert\Choice(callback={PresenceEnfantType::class, "toArray"})
     */
    public $type;

    /**
     * @var DateTime Heure d'arrivée de l'enfant s'il est présent
     *
     * @ORM\Column(type="time", nullable=true)
     */
    public $heureArrivee;

    /**
     * @var DateTime Heure de départ de l'enfant s'il est présent
     *
     * @ORM\Column(type="time", nullable=true)
     */
    public $heureDepart;

    /**
     * @var string Commentaire concernant la présence / l'absence de cet enfant
     *
     * @ORM\Column(nullable=true)
     */
    public $commentaire;

    /**
     * @var Enfant L'enfant de cette présence / absence
     *
     * @ORM\ManyToOne(targetEntity="Enfant", inversedBy="presencesEnfants")
     * @ORM\JoinColumn(nullable=false)
     */
    public $enfant;


    public function getId(): ?int
    {
        return $this->id;
    }

}
