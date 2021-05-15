<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Une famille
 * @package App\Entity
 * @ORM\Entity
 * @ApiResource(
 *     itemOperations={"get"},
 *     collectionOperations={"get"}
 * )
 */
class Famille
{
    /**
     * @var int ID de la famille
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @Groups({"jour_planning"})
     */
    private $id;

    /**
     * @var string Le nom de la famille
     *
     * @ORM\Column
     * @Assert\NotBlank
     */
    public $nom;

    /**
     * @var DateTime La date d'entrée de la famille à la crèche (premier jour effectif à la crèche)
     *
     * @ORM\Column(type="date")
     * @Assert\NotNull
     */
    public $dateEntree;

    /**
     * @var DateTime La date de sortie de la famille de la crèche (dernier jour effectif à la crèche)
     *
     * @ORM\Column(type="date")
     * @Assert\NotNull
     */
    public $dateSortie;

    /**
     * @var Enfant[] Les enfants de la famille
     *
     * @ORM\OneToMany(targetEntity=Enfant::class, mappedBy="famille")
     */
    public $enfants;

    /**
     * @var CParent[] Les parents de la famille
     *
     * @ORM\OneToMany(targetEntity=CParent::class, mappedBy="famille")
     */
    public $parents;


    public function __construct()
    {
        $this->enfants = new ArrayCollection();
        $this->parents = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

}
