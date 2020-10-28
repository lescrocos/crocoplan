<?php


namespace App\Entity\NoDb;

use App\Entity\CommentaireFamilleMoisPlanning;
use App\Entity\Garde;
use App\Entity\MoisPlanning;
use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Représente les disponibilités d'une famille pour un mois donné
 * @ApiResource(normalizationContext={"groups"={"mes_dispos_du_mois"}})
 */
class MesDisposDuMois
{
    /**
     * @var string Code du mois au format "Y-m" suivi de "_" puis l'ID de la famille
     * @ApiProperty(identifier=true)
     */
    public $code;

    /**
     * @var MoisPlanning le mois planning de ces dispos du mois
     *
     * @Groups({"mes_dispos_du_mois"})
     */
    public $moisPlanning;

    /**
     * @var CommentaireFamilleMoisPlanning|null le commentaire de la famille sur ce mois
     *
     * @Groups({"mes_dispos_du_mois"})
     */
    public $commentaireFamilleMoisPlanning;

    /**
     * @var GardeDisponible[] Les gardes du mois avec l'état de disponibilité de la famille pour chacune
     *
     * @Groups({"mes_dispos_du_mois"})
     */
    public $gardesDisponibles;


    public function __construct()
    {
        $this->gardesDisponibles = [];
    }

}