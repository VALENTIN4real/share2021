<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class AjoutFichierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('nom', FileType::class, array('label' => 'Fichier à télécharger'))
        ->add('utilisateur', EntityType::class, array('class'=>'App\Entity\Utilisateur',
        'choice_label'=>function($utilisateur){
            return $utilisateur->getNom().' '.$utilisateur->getPrenom();
        }))
        ->add('theme', EntityType::class, array('class'=>'App\Entity\Theme',
        'choice_label'=>'nom', 'mapped' => false))
        ->add('themes', EntityType::class, array('label' => 'Thèmes à relier', 'class' =>'App\Entity\Theme','choice_label'=>'nom','expanded' => true, 'multiple' => true))
        ->add('valider', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
