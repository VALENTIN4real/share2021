<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Utilisateur;

class UtilisateurController extends AbstractController
{
    #[Route('/utilisateur', name: 'utilisateur')]
    public function index(): Response
    {
        return $this->render('utilisateur/index.html.twig', [
            'controller_name' => 'UtilisateurController',
        ]);
    }

    #[Route('/liste-utilisateur', name: 'listeUtilisateur')]
    public function listeUtilisateur(): Response
    {
        $repoUtilisateur = $this->getDoctrine()->getRepository(Utilisateur::class);
        $utilisateurs = $repoUtilisateur->findBy(array(),array('nom'=>'ASC'));
        
        return $this->render('utilisateur/index.html.twig', ['utilisateurs' => $utilisateurs]);
    }
}
