<?php

namespace App\Entity;

use App\Enum\AbsenceProType;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Représente la présence ou l'absence d'un·e professionnel·le pour un jour de planning donné.
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(columns={"jour_planning_id", "pro_id"})})
 */
class PresencePro
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
     * @var bool true si la·le pro est présent·e, false sinon
     *
     * @ORM\Column(nullable=true, type="boolean")
     *
     * @Groups({"jour_planning"})
     */
    public $present;

    /**
     * @var string Type d'absence de la·le pro
     *
     * @ORM\Column(nullable=true)
     * @Assert\Choice(callback={AbsenceProType::class, "toArray"})
     *
     * @Groups({"jour_planning"})
     */
    public $absenceType;

    /**
     * @var DateTime Heure d'arrivée de la·le pro s'il·elle est présent·e
     *
     * @ORM\Column(type="time", nullable=true)
     *
     * @Groups({"jour_planning"})
     */
    public $heureArrivee;

    /**
     * @var DateTime Heure de départ de la·le pro s'il·elle est présent·e
     *
     * @ORM\Column(type="time", nullable=true)
     *
     * @Groups({"jour_planning"})
     */
    public $heureDepart;

    /**
     * @var string Commentaire concernant cette présence / absence
     *
     * @ORM\Column(nullable=true)
     *
     * @Groups({"jour_planning"})
     */
    public $commentaire;

    /**
     * @var DateTime La version cette présence / absence (permet "l'optimistic-locking" https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/reference/transactions-and-concurrency.html#optimistic-locking )
     *
     * @ORM\Version @ORM\Column(type="datetime", nullable=false)
     */
    public $version;

    /**
     * @var JourPlanning Le jour de planning de cette présence
     *
     * @ORM\ManyToOne(targetEntity=JourPlanning::class, inversedBy="presencesPros")
     * @ORM\JoinColumn(nullable=false)
     */
    public $jourPlanning;

    /**
     * @var Pro Le·la pro concerné·e par cette présence / absence
     *
     * @ORM\ManyToOne(targetEntity=Pro::class)
     * @ORM\JoinColumn(nullable=false)
     *
     * @Groups({"jour_planning"})
     */
    public $pro;


    public function getId(): ?int
    {
        return $this->id;
    }

}
