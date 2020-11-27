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
class Pro
{
    /**
     * @var int ID du·de la professionnel·le
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @Groups({"jour_planning"})
     */
    private $id;

    /**
     * @var string Le nom du·de la professionnel·le
     *
     * @ORM\Column
     * @Assert\NotBlank
     */
    public $nom;

    /**
     * @var string|null L'email du·de la professionnel·le
     *
     * @ORM\Column(nullable=true)
     * @Assert\Email
     */
    public $email;

    /**
     * @var PresencePro[] Les présences / absences de ce·tte pro
     *
     * @ORM\OneToMany(targetEntity="PresencePro", mappedBy="pro")
     */
    public $presences;


    public function __construct()
    {
        $this->presences = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

}
