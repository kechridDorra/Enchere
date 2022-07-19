<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Categorie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
	   
	    $builder
		    ->add('titre')
		    ->add('description')
		    ->add('prixInitial')
		    ->add('categorie', EntityType::class, [
			    'class' => Categorie::class
		    ])
		    ->add('images', FileType::class,
			    ['required' => false,
				    'multiple' => true,
				    'mapped' => false
			    ])
	        ->add('image', FileType::class, array('label'=>'Upload Image'));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
