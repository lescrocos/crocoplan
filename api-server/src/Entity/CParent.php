<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
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
     *
     * @Groups("authentication_success")
     */
    private $id;

    /**
     * @var Famille La famille du parent
     *
     * @ORM\ManyToOne(targetEntity=Famille::class, inversedBy="parents")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Groups("authentication_success")
     */
    private $famille;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Famille
     */
    public function getFamille(): Famille
    {
        return $this->famille;
    }

    /**
     * @param Famille $famille
     */
    public function setFamille(Famille $famille): void
    {
        $this->famille = $famille;
    }

}
