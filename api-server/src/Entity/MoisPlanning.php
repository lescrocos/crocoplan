<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Action\NotFoundAction;
use App\Controller\MoisPlanningController;

/**
 * Un mois de planning
 * @package App\Entity
 * @ORM\Entity
 * @ApiResource(
 *     itemOperations={
 *         "get"={"controller"=NotFoundAction::class, "read"=false, "output"=false},
 *         "recalculer_compteurs_familles"={
 *             "path"="/mois-plannings/{id}/recalculer-compteurs-familles",
 *             "controller"="App\Controller\MoisPlanningController::recalculerCompteursFamilles",
 *             "method"="put",
 *             "normalization_context"={"groups"="recalculer_compteurs_familles"},
 *             "denormalization_context"={"groups"="recalculer_compteurs_familles"},
 *         }
 *     },
 *     collectionOperations={"get"={"controller"=NotFoundAction::class, "read"=false, "output"=false}}
 * )
 */
class MoisPlanning
{
    /**
     * @var int ID du jour de planning
     * @ApiProperty(identifier=false)
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string Le code du mois avec ce format 'Y-m' par exemple '2020-02'
     * @ApiProperty(identifier=true)
     *
     * @ORM\Column(length=7, unique=true)
     * @Assert\NotNull
     *
     * @Groups({"mes_dispos_du_mois"})
     */
    public $code;

    /**
     * @var DateTime La date du premier lundi de ce mois planning
     *
     * @ORM\Column(type="date")
     * @Assert\NotNull
     *
     * @Groups({"mes_dispos_du_mois"})
     */
    public $dateDebut;

    /**
     * @var DateTime La date du dernier dimanche de ce mois planning
     *
     * @ORM\Column(type="date")
     * @Assert\NotNull
     *
     * @Groups({"mes_dispos_du_mois"})
     */
    public $dateFin;

    /**
     * @var ?string Commentaire de ce mois de planning
     *
     * @ORM\Column(nullable=true)
     *
     * @Groups({"mes_dispos_du_mois"})
     */
    public $commentaire;

    /**
     * @var ?int Nombre de jours de quota d'absence famille à ré-initialiser pour chaque famille à la date indiquée par
     *
     * @ORM\Column(nullable=true)
     */
    public $reinitQuotaJoursAbsenceNombre;

    /**
     * @var ?DateTime La date du dernier dimanche de ce mois planning
     *
     * @ORM\Column(type="date", nullable=true)
     */
    public $reinitQuotaJoursAbsenceDate;

    /**
     * @var JourPlanning[] Les jours planning de ce mois
     *
     * @ORM\OneToMany(targetEntity=JourPlanning::class, mappedBy="moisPlanning")
     */
    public $joursPlanning;

    /**
     * @var CompteurFamilleMoisPlanning[] Les compteurs des familles de ce mois
     *
     * @ORM\OneToMany(targetEntity=CompteurFamilleMoisPlanning::class, mappedBy="moisPlanning")
     *
     * @Groups({"recalculer_compteurs_familles"})
     */
    public $compteursFamilles;

    /**
     * @var ?MoisPlanning Le mois planning précédent celui-ci
     *
     * @ORM\ManyToOne(targetEntity=MoisPlanning::class)
     */
    public $moisPlanningPrecedent;


    public function __construct()
    {
        $this->joursPlanning = new ArrayCollection();
        $this->compteursFamilles = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

}
