<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\FileUploader;
use App\Form\AjoutFichierType;
use App\Entity\Fichier;
use App\Entity\Theme;


class FichierController extends AbstractController
{
    #[Route('/ajout-fichier', name: 'ajout-fichier')]
    public function ajoutFichier(Request $request): Response
    {
        $fichier = new Fichier();
        $form = $this->createForm(AjoutFichierType::class, $fichier);
        $doctrine = $this->getDoctrine();
        $fichiers = $doctrine->getRepository(Fichier::class)->findBy(array(), array('date'=>'DESC'));

        if($request->isMethod('POST')){
          $form->handleRequest($request);
          if ($form->isSubmitted() && $form->isValid()){

            //$idTheme = $form->get('theme')->getData();
            //$theme = $this->getDoctrine()->getRepository(Theme::class)->find($idTheme);
              
            // Récupération du fichier
            $fichierPhysique = $fichier->getNom();

            $fichier->setDate(new \DateTime());
            $ext = '';

            if($fichierPhysique->guessExtension()!= null ){
              $ext = $fichierPhysique->guessExtension();
            }
            $fichier->setOriginal($fichierPhysique->getClientOriginalName());
            $fichier->setExtension($ext);
            $fichier->setTaille($fichierPhysique->getSize());
            $fichier->setNom(md5(uniqid()));
            //$fichier->addTheme($theme);
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
          'form' => $form->createView(),
          'fichiers' => $fichiers
        ]);
    }
}