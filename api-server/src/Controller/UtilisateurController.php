<?php


namespace App\Controller;


use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UtilisateurController extends AbstractController
{
    public function getMe(): Utilisateur
    {
        $user = $this->getUser();
        if (!empty($user)) {
            return $user;
        } else {
            throw new AccessDeniedHttpException();
        }
    }
}