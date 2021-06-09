<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Un·e professionnel·le
 * @package App\Entity
 * @ORM\Entity
 * @ApiResource(
 *     itemOperations={"get"},
 *     collectionOperations={"get"}
 * )
 */
class Pro extends Utilisateur
{

    /**
     * @Groups({"jour_planning"})
     */
    public function getId(): ?int
    {
        return parent::getId();
    }

}
