<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Un mois de planning
 * @package App\Entity
 * @ORM\Entity
 */
class MoisPlanning
{
    /**
     * @var int ID du jour de planning
     * @ApiProperty(identifier=false)
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string Le code du mois avec ce format 'Y-m' par exemple '2020-02'
     * @ApiProperty(identifier=true)
     *
     * @ORM\Column(length=7, unique=true)
     * @Assert\NotNull
     *
     * @Groups({"mes_dispos_du_mois"})
     */
    public $code;

    /**
     * @var DateTime La date du premier lundi de ce mois planning
     *
     * @ORM\Column(type="date")
     * @Assert\NotNull
     *
     * @Groups({"mes_dispos_du_mois"})
     */
    public $dateDebut;

    /**
     * @var DateTime La date du dernier dimanche de ce mois planning
     *
     * @ORM\Column(type="date")
     * @Assert\NotNull
     *
     * @Groups({"mes_dispos_du_mois"})
     */
    public $dateFin;

    /**
     * @var string Commentaire de ce mois de planning
     *
     * @ORM\Column(nullable=true)
     *
     * @Groups({"mes_dispos_du_mois"})
     */
    public $commentaire;

    /**
     * @var JourPlanning[] Les jours planning de ce mois
     *
     * @ORM\OneToMany(targetEntity="JourPlanning", mappedBy="moisPlanning")
     */
    public $joursPlanning;


    public function __construct()
    {
        $this->joursPlanning = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

}
