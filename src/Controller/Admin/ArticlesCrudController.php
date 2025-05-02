<?php

namespace App\Controller\Admin;

use App\Entity\Articles;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ArticlesCrudController extends AbstractCrudController
{

    private Security $security;
    public function __construct(Security $security)
    {
        $this->security =$security;
    }

    public static function getEntityFqcn(): string
    {
        return Articles::class;
    }

    // Insertion d'un article
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void // est appelé juste avant l'insertion de n'importe quel objet
    {
        if (!$entityInstance instanceof Articles) return; // on vérifie que l'objet qu'on est entrain d'insérer est bien un article
        $user = $this->security->getUser(); // on récupére l'utilisateur actuellement connecté grâce au service Security de symfony
        $entityInstance->setUser($user); // On dit à l'article  ton user est celui qui est connecté maintenant

        parent::persistEntity($entityManager, $entityInstance);
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm()->hideOnIndex(),
            TextField::new('title'),
            TextareaField::new('content'),
            ImageField::new('image')->setUploadDir('/public/images/') // C’est le chemin sur le serveur où EasyAdmin va stocker les images uploadées.
            ->setBasePath('images/') // Ce chemin est utilisé pour afficher les images dans l’interface d’administration.
            ->setUploadedFileNamePattern('[randomhash].[extension]'), // Cela indique comment sera renommée l’image une fois téléversée.
            DateTimeField::new('createdAt')->hideOnForm(),
            DateTimeField::new('updatedAt')->hideOnForm(),
            AssociationField::new('category','Category')->setFormTypeOption('choice_label','name')     
            ->setLabel('Catégories')
            ->formatValue(function ($value, $entity) {
                // Récupère les noms des catégories
                return implode(', ', $entity->getCategory()->map(function($category) {return $category->getName();})->toArray());
            }),
            AssociationField::new('user','Users')->hideOnForm()

            
            
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions

            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
    
}
