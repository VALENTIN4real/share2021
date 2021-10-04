<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\AjoutFichierType;
use App\Entity\Fichier;


class FichierController extends AbstractController
{
    #[Route('/ajout-fichier', name: 'ajout-fichier')]
    public function ajoutFichier(Request $request): Response
    {
        $fichier = new Fichier();
        $form = $this->createForm(AjoutFichierType::class, $fichier);

        if($request->isMethod('POST')){
          $form->handleRequest($request);
          if ($form->isSubmitted() && $form->isValid()){
              
            // Récupération du fichier
            $fichierPhysique = $fichier->getNom();

            $fichier->setDate(new \DateTime());
            $fichier->setExtension($fichierPhysique->guessExtension());
            $fichier->setTaille($fichierPhysique->getSize());
            $fichier->setNom(md5(uniqid()));
            try{
              $fichierPhysique->move($this->getParameter('file_directory'), $fichier->getNom());
              $this->addFlash('notice', 'Fichier envoyé');
              $em = $this->getDoctrine()->getManager();
              $em->persist($fichier);
              $em->flush();
            }
            catch(FileException $e){
              $this->addFlash('notice', 'Erreur d\'envoi');
            }


            return $this->redirectToRoute('ajout-fichier');
          }
      }

        return $this->render('fichier/ajout-fichier.html.twig', [
          'form' => $form->createView()
        ]);
    }
}