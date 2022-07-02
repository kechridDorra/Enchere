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
	        ->add('adresse')
	        ->add('ville')
	        ->add('codePostal')
	        ->add('numeroCarte')
	        ->add('codeSecurite')
	        ->add('moisExp')
	        ->add('anneeExp')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
	
	    $resolver->setDefaults([
		    'data_class' => User::class,
	    
        ]);
    }
}
