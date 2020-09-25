<?php


namespace App\Entity;


use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Une garde de parent
 * @package App\Entity
 * @ORM\Entity
 * @ApiResource
 */
class Garde
{

    /**
     * @var int ID de la garde
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var DateTime L'heure de début de la garde
     *
     * @ORM\Column(type="time")
     */
    public $heureArrivee;

    /**
     * @var DateTime L'heure de fin de la garde
     *
     * @ORM\Column(type="time")
     */
    public $heureDepart;

    /**
     * @var string Commentaire concernant la garde
     *
     * @ORM\Column(nullable=true)
     * @Assert\NotBlank
     */
    public $commentaire;

    /**
     * @var DateTime La version de cette garde (permet "l'optimistic-locking" https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/reference/transactions-and-concurrency.html#optimistic-locking )
     *
     * @ORM\Version @ORM\Column(type="datetime", nullable=false)
     */
    public $version;

    /**
     * @var CoefFamille Le jour de planning de cette garde
     *
     * @ORM\ManyToOne(targetEntity="JourPlanning", inversedBy="gardes")
     * @ORM\JoinColumn(nullable=false)
     */
    public $jourPlanning;

    /**
     * @var Famille La famille qui est affectée à la garde
     *
     * @ORM\ManyToOne(targetEntity="Famille", inversedBy="gardes")
     */
    public $famille;

    /**
     * @var Famille[] Les familles disponibles pour cette garde
     *
     * @ORM\ManyToMany(targetEntity="Famille", mappedBy="gardesDisponibles")
     */
    public $familleDisponibles;


    public function __construct()
    {
        $this->familleDisponibles = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

}
