<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;
use App\Enum\AbsenceEnfantType;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Représente la présence ou l'absence d'un enfant.
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(columns={"jour_planning_id", "enfant_id"})})
 * @ApiResource
 */
class PresenceEnfant
{
    /**
     * @var int ID de cette présence / absence
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var boolean true si l'enfant est présent, false sinon
     *
     * @ORM\Column(nullable=true)
     */
    public $present;

    /**
     * @var string Type d'absence de l'enfant
     *
     * @ORM\Column(nullable=true)
     * @Assert\Choice(callback={AbsenceEnfantType::class, "toArray"})
     */
    public $absenceType;

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
     * @var string Commentaire concernant cette présence / absence
     *
     * @ORM\Column(nullable=true)
     */
    public $commentaire;

    /**
     * @var DateTime La version cette présence / absence (permet "l'optimistic-locking" https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/reference/transactions-and-concurrency.html#optimistic-locking )
     *
     * @ORM\Version @ORM\Column(type="datetime", nullable=false)
     */
    public $version;

    /**
     * @var CoefFamille Le jour de planning de cette présence
     *
     * @ORM\ManyToOne(targetEntity="JourPlanning", inversedBy="presencesEnfants")
     * @ORM\JoinColumn(nullable=false)
     */
    public $jourPlanning;

    /**
     * @var Enfant L'enfant concerné par cette présence / absence
     *
     * @ORM\ManyToOne(targetEntity="Enfant", inversedBy="presences")
     * @ORM\JoinColumn(nullable=false)
     */
    public $enfant;


    public function getId(): ?int
    {
        return $this->id;
    }

}
