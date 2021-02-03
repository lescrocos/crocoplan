<?php


namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * L'association d'un enfant à un groupe d'enfant
 * @package App\Entity
 * @ORM\Entity
 */
class EnfantGroupeEnfant
{
    /**
     * @var int ID de l'association
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var DateTime La date du début où l'enfant doit être associé à ce groupe d'enfant
     *
     * @ORM\Column(type="date")
     * @Assert\NotNull
     *
     * @Groups({"enfant"})
     */
    public $dateDebut;

    /**
     * @var DateTime La date de fin où l'enfant doit être associé à ce groupe d'enfant
     *
     * @ORM\Column(type="date")
     * @Assert\NotNull
     *
     * @Groups({"enfant"})
     */
    public $dateFin;

    /**
     * @var Enfant l'enfant de cette association
     *
     * @ORM\ManyToOne(targetEntity=Enfant::class, inversedBy="groupes")
     * @ORM\JoinColumn(nullable=false)
     */
    public $enfant;

    /**
     * @var GroupeEnfant le groupe d'enfant de cette association
     *
     * @ORM\ManyToOne(targetEntity=GroupeEnfant::class)
     * @ORM\JoinColumn(nullable=false)
     *
     * @Groups({"enfant"})
     */
    public $groupe;


    public function getId(): ?int
    {
        return $this->id;
    }
}