<?php


namespace App\Entity\NoDb;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\CommentaireFamilleMoisPlanning;
use App\Entity\MoisPlanning;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Représente les disponibilités d'une famille pour un mois donné
 * @ApiResource(
 *     normalizationContext={"groups"={"mes_dispos_du_mois", "mes_dispos_du_mois:read"}},
 *     denormalizationContext={"groups"={"mes_dispos_du_mois", "mes_dispos_du_mois:write"}},
 *     itemOperations={
 *         "get",
 *         "put"
 *     },
 *     collectionOperations={}
 * )
 */
class MesDisposDuMois
{
    /**
     * @var string Code du mois au format "Y-m" suivi de "_" puis l'ID de la famille
     * @ApiProperty(identifier=true)
     *
     * @Groups({"mes_dispos_du_mois"})
     */
    public $code;

    /**
     * @var MoisPlanning le mois planning de ces dispos du mois
     *
     * @Groups({"mes_dispos_du_mois:read"})
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
     * @Groups({"mes_dispos_du_mois:read"})
     */
    public $gardesDisponibles;

    /**
     * @var int[] Les ids des gardes de ce mois pour lesquelles la famille est disponible
     *
     * @Groups({"mes_dispos_du_mois:write"})
     */
    public $gardesDisponiblesIds;


    public function __construct()
    {
        $this->gardesDisponibles = [];
        $this->gardesDisponiblesIds = [];
    }

}