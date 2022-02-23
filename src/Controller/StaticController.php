<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ContactType;
use App\Form\InscriptionType;
use App\Form\CommentaireType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Contact;
use App\Entity\Commentaire;
use App\Entity\Utilisateur;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class StaticController extends AbstractController
{
    #[Route('/accueil', name: 'accueil')]
    public function accueil(): Response
    {
        return $this->render('static/accueil.html.twig', []);
    }

    #[Route('/inscriptionComplete', name: 'inscriptionComplete')]
    public function inscriptionComplete(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        //chien de martin
        $utilisateur = new Utilisateur();
        $user = new User();
        $utilisateur->setUserData($user);
        $user->setUtilisateurData($utilisateur);
        $form = $this->createForm(InscriptionType::class,$utilisateur);
        if ($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isSubmitted()&&$form->isValid()){

                $user->setEmail($form->get('email')->getData());
                $user->setPassword($passwordHasher->hashPassword($user,$form->get('password')->getData()));
                $user->setRoles(array('ROLE_USER'));

                $em = $this->getDoctrine()->getManager();

                $em->persist($utilisateur);
                $em->persist($user);

                $em->flush();
                return $this->redirectToRoute('inscriptionComplete');
            }
        }

        return $this->render('static/inscriptionComplete.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/listeContact', name: 'listeContact')]
    public function listeContact(): Response
    {
        $repoContact = $this->getDoctrine()->getRepository(Contact::class);
        $contacts = $repoContact->findBy(array(),array('nom'=>'ASC'));
        
        return $this->render('static/listeContact.html.twig', ['contacts' => $contacts]);
    }

    #[Route('/listeCommentaire', name: 'listeCommentaire')]
    public function listeCommentaire(): Response
    {
        $repoCommentaire = $this->getDoctrine()->getRepository(Commentaire::class);
        $commentaires = $repoCommentaire->findBy(array(),array('nom'=>'ASC'));
        
        return $this->render('static/listeCommentaire.html.twig', ['commentaires' => $commentaires]);
    }


    #[Route('/contact', name: 'contact')]
    public function contact(Request $request,\Swift_Mailer $mailer): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class,$contact);
        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                
                $this->addFlash('notice','Bouton appuyé');
                $message = (new \Swift_Message($contact->getSujet()))
                ->setFrom($contact->getEmail())
                ->setTo('mairesse.valentin.pro@gmail.com')
                //->setBody($form->get('message')->getData());
                ->setBody($this->renderView('emails/contact-email.html.twig',array(
                'nom'=>$contact->getNom(),
                'sujet'=>$contact->getSujet(),
                'contenu'=>$contact->getMessage())),'text/html');
                $mailer->send($message);

                $em = $this->getDoctrine()->getManager();
                $em->persist($contact);
                $em->flush();

                return $this->redirectToRoute('contact');
            }
        }
        return $this->render('static/contact.html.twig', ['form' => $form -> createView()]);
    }
    

    #[Route('/commentaire', name: 'commentaire')]
    public function commentaire(Request $request): Response
    {
        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class,$commentaire);
        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $this->addFlash('notice','Bouton appuyé ' . $nom);

                $em = $this->getDoctrine()->getManager();
                $em->persist($commentaire);
                $em->flush();

                return $this->redirectToRoute('commentaire');
            }
        }
        return $this->render('static/commentaire.html.twig', ['form' => $form -> createView()]);
    }
}