<?php
namespace AppBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class ProfilController extends Controller
{
    /**
     * @Route("/profil",name="user_profil")
     */
    public function indexAction()
    {
        return $this->render('user/profil.html.twig');
    }
}