<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Un groupe d'enfant
 * @package App\Entity
 * @ORM\Entity
 */
class GroupeEnfant
{
    /**
     * @var int ID du groupe de l'enfant
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @Groups({"enfant"})
     */
    private $id;

    /**
     * @var string Le nom du groupe
     *
     * @ORM\Column
     * @Assert\NotBlank
     *
     * @Groups({"enfant"})
     */
    public $nom;


    public function getId(): ?int
    {
        return $this->id;
    }
}