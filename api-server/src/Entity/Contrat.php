<?php


namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Le contrat de présence à la crèche d'un enfant. Il associe également le groupe de l'enfant.
 * @package App\Entity
 * @ORM\Entity
 */
class Contrat
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
     * @var DateTime La date du début du contrat de présence de cet enfant à la crèche
     *
     * @ORM\Column(type="date")
     * @Assert\NotNull
     *
     * @Groups({"enfant"})
     */
    public $dateDebut;

    /**
     * @var DateTime La date de fin du contrat de présence de cet enfant à la crèche
     *
     * @ORM\Column(type="date")
     * @Assert\NotNull
     *
     * @Groups({"enfant"})
     */
    public $dateFin;

    /**
     * @var int Nombre de jours par semaine du contrat. Si cette valeur vaut 4 par exemple, il s'agit d'un contrat aux 4/5ème.
     *
     * @ORM\Column(type="integer", options={"default": 5})
     * @Assert\NotNull
     */
    public $nbJoursParSemaine = 5;

    /**
     * @var Enfant l'enfant de cette association
     *
     * @ORM\ManyToOne(targetEntity=Enfant::class, inversedBy="contrats")
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