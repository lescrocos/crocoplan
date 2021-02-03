<?php


namespace App\Entity;


use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Une garde de parent
 * @package App\Entity
 * @ORM\Entity
 * @ApiResource(
 *     normalizationContext={"groups"={"garde"}},
 *     attributes={"pagination_client_enabled"=true},
 *     itemOperations={"get"},
 *     collectionOperations={"get"}
 * )
 * @ApiFilter(SearchFilter::class, properties={"famille": "exact"})
 * @ApiFilter(ExistsFilter::class, properties={"famille"})
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
     * @Groups({"garde", "mes_dispos_du_mois"})
     */
    private $id;

    /**
     * @var DateTime L'heure de début de la garde
     *
     * @ORM\Column(type="time")
     *
     * @Groups({"garde", "mes_dispos_du_mois", "jour_planning"})
     */
    public $heureArrivee;

    /**
     * @var DateTime L'heure de fin de la garde
     *
     * @ORM\Column(type="time")
     *
     * @Groups({"garde", "mes_dispos_du_mois", "jour_planning"})
     */
    public $heureDepart;

    /**
     * @var string Commentaire concernant la garde
     *
     * @ORM\Column(nullable=true)
     * @Assert\NotBlank
     *
     * @Groups({"garde", "mes_dispos_du_mois", "jour_planning"})
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
     * @Groups({"garde", "mes_dispos_du_mois"})
     */
    public $jourPlanning;

    /**
     * @var Famille La famille qui est affectée à la garde
     *
     * @ORM\ManyToOne(targetEntity=Famille::class)
     *
     * @Groups({"jour_planning"})
     */
    public $famille;

    /**
     * @var Famille[] Les familles disponibles pour cette garde
     *
     * @ORM\ManyToMany(targetEntity="Famille")
     * @ORM\JoinTable(name="garde_famille_disponible")
     */
    public $famillesDisponibles;


    public function __construct()
    {
        $this->famillesDisponibles = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function addFamilleDisponible(Famille $famille) {
        if (!$this->famillesDisponibles->contains($famille)) {
            $this->famillesDisponibles->add($famille);
        }
    }

}
