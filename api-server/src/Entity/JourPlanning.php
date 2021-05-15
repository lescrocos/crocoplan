<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Un jour de planning
 * @package App\Entity
 * @ORM\Entity
 * @ApiResource(
 *     normalizationContext={"groups"={"jour_planning"}},
 *     itemOperations={"get"},
 *     collectionOperations={"get"}
 * )
 * @ApiFilter(DateFilter::class, properties={"date"})
 */
class JourPlanning
{
    /**
     * @var int ID du jour de planning
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @Groups({"jour_planning"})
     */
    private $id;

    /**
     * @var DateTime La date de ce jour de planning
     *
     * @ORM\Column(type="date")
     * @Assert\NotNull
     *
     * @Groups({"jour_planning", "garde", "mes_dispos_du_mois"})
     */
    public $date;

    /**
     * @var string Commentaire de ce jour de planning
     *
     * @ORM\Column(nullable=true)
     *
     * @Groups({"jour_planning"})
     */
    public $commentaire;

    /**
     * @var bool Indique si la crèche est ouverte pour les enfants ce jour là

     * @ORM\Column(options={"default": true})
     *
     * @Groups({"jour_planning"})
     */
    public $crecheOuvertePourEnfants = true;

    /**
     * @var MoisPlanning le mois planning de ce jour
     *
     * @ORM\ManyToOne(targetEntity=MoisPlanning::class, inversedBy="joursPlanning")
     * @ORM\JoinColumn(nullable=false)
     */
    public $moisPlanning;

    /**
     * @var PresenceEnfant[] Les présences enfants
     *
     * @ORM\OneToMany(targetEntity=PresenceEnfant::class, mappedBy="jourPlanning")
     *
     * @Groups({"jour_planning"})
     */
    public $presencesEnfants;

    /**
     * @var PresencePro[] Les présences pros
     *
     * @ORM\OneToMany(targetEntity=PresencePro::class, mappedBy="jourPlanning")
     *
     * @Groups({"jour_planning"})
     */
    public $presencesPros;

    /**
     * @var Garde[] Les gardes parent
     *
     * @ORM\OneToMany(targetEntity=Garde::class, mappedBy="jourPlanning")
     *
     * @Groups({"jour_planning"})
     */
    public $gardes;


    public function __construct()
    {
        $this->presencesEnfants = new ArrayCollection();
        $this->presencesPros = new ArrayCollection();
        $this->gardes = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

}
