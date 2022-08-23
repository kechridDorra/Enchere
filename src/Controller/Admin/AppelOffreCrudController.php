<?php

namespace App\Controller\Admin;

use App\Entity\AppelOffre;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;

class AppelOffreCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AppelOffre::class;
    }
	
	public function configureFields(string $pageName): iterable
	{
		return [
			yield IdField::new('id')->hideOnForm(),
			yield TextField::new('titre'),
			yield TextField::new('description'),
			yield NumberField::new('prix'),
			yield ImageField::new('image')->setUploadDir('/public/uploads/'),
		];
	}
}
