<?php


namespace App\Controller;


use App\Entity\MoisPlanning;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MoisPlanningController extends AbstractController
{
    public function recalculerCompteursFamilles(MoisPlanning $data): MoisPlanning
    {
        $moisPlanning = $data;

        // TODO recalculer les compteurs

        return $moisPlanning;
    }
}