<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;
use App\Enum\PresenceProType;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Représente la présence ou l'absence d'un·d'une professionnel·le pour un jour donné
 * @package App\Entity
 * @ORM\Entity
 * @ApiResource
 */
class PresencePro
{
    /**
     * @var int ID de cette présence / absence d'un·d'une professionnel·le
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var DateTime La date de cette présence / absence d'un·d'une professionnel·le
     *
     * @ORM\Column(type="date")
     * @Assert\NotNull
     */
    public $date;

    /**
     * @var string Type de présence du·de la professionnel·le
     *
     * @ORM\Column
     * @Assert\NotNull
     * @Assert\Choice(callback={PresenceProType::class, "toArray"})
     */
    public $type;

    /**
     * @var DateTime Heure d'arrivée du·de la professionnel·le s'il·si elle est présent·e
     *
     * @ORM\Column(type="time", nullable=true)
     */
    public $heureArrivee;

    /**
     * @var DateTime Heure de départ du·de la professionnel·le s'il·si elle est présent·e
     *
     * @ORM\Column(type="time", nullable=true)
     */
    public $heureDepart;

    /**
     * @var string Commentaire concernant la présence / l'absence de ce·cette professionnel·le
     *
     * @ORM\Column(nullable=true)
     */
    public $commentaire;

    /**
     * @var Pro Le·la professionnel·le de cette présence / absence
     *
     * @ORM\ManyToOne(targetEntity="Pro", inversedBy="presences")
     * @ORM\JoinColumn(nullable=false)
     */
    public $pro;


    public function getId(): ?int
    {
        return $this->id;
    }

}
