<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\RegisterType;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function index(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface, EntityManagerInterface $entityManager, CategoriesRepository $categoriesRepository): Response //le mécanisme d'injection de dépendance en symfony => il s'agit de dire au symfony que je veux que tu rentre dans cette action en ambarquant avec toi l'objet $request qui est une instance stocké dans une variable 
    {


        if ($this->getUser()) {   // si l'utilisateur est connécté 

            return $this->redirectToRoute('app_account'); // je le redirige vers la page account
        }

        $user = new Users; //On crée un objet de la classe Users et on le stock dans la vairiable $user

        $form = $this->createForm(RegisterType::class, $user); // cette méthode prend en paramètres : la classe du formulaire et l'objet gérer par le formulaire

        $form->handleRequest($request); // cette méthode est utilisée pour traiter les données soumises par l'utilisateur
        // Il faut que mon formulaire écoute et analyse la requete qui viens de la vue et vérifier s'il y a un post envoyé ou pas 
        // On utilise l'objet request créé par symfony et qui représente la requete HTTP entrante ( ici la requete contient des données de formulaire ) 
        if ($form->isSubmitted() && $form->isValid()) {
            // il faut hasher le mdp
            $password = $form->get('password')->getData();
            $passwordHasher = $userPasswordHasherInterface->hashPassword($user, $password);
            $user->setPassword($passwordHasher);

            $entityManager->persist($user);
            $entityManager->flush();

            return  $this->redirectToRoute('app_login');
        }

        $categories = $categoriesRepository->findAll(); // On utilise la méthode findAll() du repository pour récupérer les catégories de la base de données
        return $this->render('register/register.html.twig', [

            'formIncription' => $form->createView(),
            'categories' => $categories, // On utilise la méthode findAll() du repository pour récupérer les catégories de la base de données

        ]);
    }
}
