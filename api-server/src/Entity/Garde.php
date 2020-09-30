<?php


namespace App\Entity;


use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;

/**
 * Une garde de parent
 * @package App\Entity
 * @ORM\Entity
 * @ApiResource(normalizationContext={"groups"={"garde"}})
 * @ApiFilter(SearchFilter::class, properties={"famille": "exact"})
 * @ApiFilter(DateFilter::class, properties={"jourPlanning.date"})
 */
class Garde
{

    /**
     * @var int ID de la garde
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @Groups({"garde"})
     */
    private $id;

    /**
     * @var DateTime L'heure de début de la garde
     *
     * @ORM\Column(type="time")
     *
     * @Groups({"garde"})
     */
    public $heureArrivee;

    /**
     * @var DateTime L'heure de fin de la garde
     *
     * @ORM\Column(type="time")
     *
     * @Groups({"garde"})
     */
    public $heureDepart;

    /**
     * @var string Commentaire concernant la garde
     *
     * @ORM\Column(nullable=true)
     * @Assert\NotBlank
     *
     * @Groups({"garde"})
     */
    public $commentaire;

    /**
     * @var DateTime La version de cette garde (permet "l'optimistic-locking" https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/reference/transactions-and-concurrency.html#optimistic-locking )
     *
     * @ORM\Version @ORM\Column(type="datetime", nullable=false)
     */
    public $version;

    /**
     * @var JourPlanning Le jour de planning de cette garde
     *
     * @ORM\ManyToOne(targetEntity="JourPlanning", inversedBy="gardes")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Groups({"garde"})
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
