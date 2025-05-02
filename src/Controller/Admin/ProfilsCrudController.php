<?php

namespace App\Controller\Admin;

use App\Entity\Profils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class ProfilsCrudController extends AbstractCrudController
{

    private Security $security;
    public function __construct(Security $security)
    {
        $this->security =$security;
    }
    public static function getEntityFqcn(): string
    {
        return Profils::class;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void // est appelé juste avant l'insertion de n'importe quel objet
    {
        if (!$entityInstance instanceof Profils) return; // on vérifie que l'objet qu'on est entrain d'insérer est bien un article
        $user = $this->security->getUser(); // on récupére l'utilisateur actuellement connecté grâce au service Security de symfony
        $entityInstance->setUser($user); // On dit à l'article  ton user est celui qui est connecté maintenant

        parent::persistEntity($entityManager, $entityInstance);
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            
            IdField::new('id')->hideOnForm()->hideOnIndex(), 
            TextareaField::new('description'),
            ImageField::new('picture')->setUploadDir('/public/images/profils') // C’est le chemin sur le serveur où EasyAdmin va stocker les images uploadées.
            ->setBasePath('images/profils') // Ce chemin est utilisé pour afficher les images dans l’interface d’administration.
            ->setUploadedFileNamePattern('[randomhash].[extension]'), // Cela indique comment sera renommée l’image une fois téléversée.
            DateField::new('dateBirth'),
            DateTimeField::new('createdAt')->hideOnForm(),
            DateTimeField::new('updatedAt')->hideOnForm(),
            AssociationField::new('user')->hideOnForm(),

        ];
    }
    
}
