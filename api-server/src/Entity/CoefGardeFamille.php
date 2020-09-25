<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Un coefficient de garde famille modifié sur une période donnée
 * @package App\Entity
 * @ORM\Entity
 * @ApiResource
 */
class CoefGardeFamille
{
    /**
     * @var int ID du jour de planning
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var float Le coefficient de garde associé à la famille pour cette période
     *
     * @ORM\Column
     * @Assert\NotNull
     */
    public $coef;

    /**
     * @var DateTime La date de début de la période d'application de ce coefficient de garde
     *
     * @ORM\Column(type="date")
     * @Assert\NotNull
     */
    public $dateDebut;

    /**
     * @var DateTime La date de fin de la période d'application de ce coefficient de garde
     *
     * @ORM\Column(type="date")
     * @Assert\NotNull
     */
    public $dateFin;

    /**
     * @var string Commentaire expliquant pourquoi ce coef
     *
     * @ORM\Column
     * @Assert\NotNull
     */
    public $commentaire;

    /**
     * @var Famille la famille concerné par ce coef
     *
     * @ORM\ManyToOne(targetEntity="Famille")
     * @ORM\JoinColumn(nullable=false)
     */
    public $famille;


    public function getId(): ?int
    {
        return $this->id;
    }

}
