<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Un parent
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="parent")
 */
class CParent extends Utilisateur
{
    /**
     * @var int ID du parent
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Famille La famille du parent
     *
     * @ORM\ManyToOne(targetEntity=Famille::class, inversedBy="parents")
     * @ORM\JoinColumn(nullable=false)
     */
    public $famille;

    public function getId(): ?int
    {
        return $this->id;
    }

}
