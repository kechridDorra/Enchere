<?php

namespace App\Form;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
	        ->add('prenom')
	        ->add('email')
	        ->add('password')
	        ->add('telephone')
	        ->add('genre')
	        ->add('adresse')
	        ->add('ville')
	        ->add('codePostal')
	        ->add('typeCarte')
	        ->add('numeroCarte')
	        ->add('codeSecurite')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
	
	    $resolver->setDefaults([
		    'data_class' => User::class,
	    
        ]);
    }
}