<?php

namespace App\Controller;

use App\Repository\ArticlesRepository;
use App\Repository\CategoriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{

    // "/home" => le chemin de cette route
    // "app_home" => le nom de la route qui nous servira au moment de l'appelle des routes dans twig
    #[Route('/', name: 'app_home')]
    public function index(ArticlesRepository $articlesRepository, CategoriesRepository $categoriesRepository): Response
    {
        // On va chercher tous les articles de la base de données

        $limit = 3; // On limite le nombre d'articles à 3
        $articles = $articlesRepository->findRecentsAticles($limit); // On utilise la méthode findRecentsAticles() du repository pour récupérer les articles récents
        // On va chercher les 3 derniers articles de la base de données 

        $categories = $categoriesRepository->findAll(); // On utilise la méthode findAll() du repository pour récupérer les catégories de la base de données 
        //ensuite on va les afficher dans la vue 
        return $this->render('home/home.html.twig', [
            'articles' => $articles,
            'title' => 'Nos articles récents',
            'limit' => $limit,
            'categories' => $categories,

        ]);
    }

    #[Route('/allArticles', name: 'app_home_all')]
    public function allArticles(ArticlesRepository $articlesRepository, CategoriesRepository $categoriesRepository, $id): Response
    {
        // On va chercher tous les articles de la base de données
        $articles = $articlesRepository->findAll();

        return $this->render('home/home.html.twig', [
            'articles' => $articles,
            'title' => 'Nos articles',

        ]);
    }

    //
    #[Route('/article/{id}', name: 'app_one_article')]
    public function showArticle(ArticlesRepository $articlesRepository, CategoriesRepository $categoriesRepository, $id): Response
    {
        //$idArticle = $articlesRepository->find();
        // On va chercher tous les articles de la base de données
        $article = $articlesRepository->findOneBy(['id' => $id]);
        $title = $article->getTitle(); // On utilise la méthode findRecentsAticles() du repository pour récupérer les articles récents

        return $this->render('home/home.html.twig', [
            'article' => $article,
            'title' => 'Article : ' . $title,
        ]);
    }

    #[Route('/category/{id}', name: 'app_category_articles')]
    public function showArticleByCategory(ArticlesRepository $articlesRepository, $id, CategoriesRepository $categoriesRepository): Response
    {
        $categories = $categoriesRepository->findOneById($id); // On utilise la méthode findAll() du repository pour récupérer les catégories de la base de données

        dd($categories);
        return $this->render('home/home.html.twig', [
            'title' => 'Nos articles',
            'categories' => $categories,
        ]);
    }
}
