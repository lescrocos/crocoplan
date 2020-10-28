<?php


namespace App\Entity\NoDb;


use App\Entity\Garde;
use Symfony\Component\Serializer\Annotation\Groups;

class GardeDisponible
{
    /**
     * @var Garde garde
     *
     * @Groups({"mes_dispos_du_mois"})
     */
    public $garde;

    /**
     * @var bool true si la famille est disponible
     *
     * @Groups({"mes_dispos_du_mois"})
     */
    public $familleDisponible;

}