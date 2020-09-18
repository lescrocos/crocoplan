<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Un enfant
 * @package App\Entity
 * @ORM\Entity
 * @ApiResource
 */
class Enfant
{
    /**
     * @var int ID de l'enfant
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string Le nom de l'enfant
     *
     * @ORM\Column
     * @Assert\NotBlank
     */
    public $nom;

    /**
     * @var Famille La famille de l'enfant
     *
     * @ORM\ManyToOne(targetEntity="Famille", inversedBy="enfants")
     */
    public $famille;

    public function getId(): ?int
    {
        return $this->id;
    }

}
