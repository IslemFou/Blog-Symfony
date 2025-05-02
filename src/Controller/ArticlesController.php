<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Form\ArticlesType;
use App\Repository\ArticlesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/account/articles')]
final class ArticlesController extends AbstractController
{
    #[Route(name: 'app_my_articles', methods: ['GET'])]
    public function index(ArticlesRepository $articlesRepository): Response
    {

        $user = $this->getUser(); // recuperation de l'utilisateur connecté
        return $this->render('articles/index.html.twig', [
            'articles' => $articlesRepository->findByUser($user),
        ]);
    }

    #[Route('/new', name: 'app_articles_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $article = new Articles();
        $form = $this->createForm(ArticlesType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser(); // recuperation de l'utilisateur connecté
            $article->setUser($user); // on stock l'utilisateur dans l'instance $article

            // recuperation de l'image//Gestion de l'image
            $image = $form->get('image')->getData();

            //le nom d'origine de l'image
            $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);

            //sécuriser le nom de l'image
            $safeImage = $slugger->slug($originalFilename);

            //Generation d'un nom unique de l'image en ajoutant un hash aléatoire
            $newImage = $safeImage . '-' . uniqid() . '.' . $image->guessExtension();

            try {
                //Déplacement de l'image dans le dossier public/images/profils

                $image->move($this->getParameter('article_directory'), $newImage);
            } catch (FileException $th) {

                $this->addFlash('error', 'Erreur lors de l\'upload de l\'image');
            }



            //mise a jour de la propriété picture de l'entité Profil
            $article->setImage($newImage);

            //récupération de la catégorie sélectionnée dans le formulaire
            $categories = $article->getCategory()->getValues();

            foreach ($categories as $category) {

                $category->addArticle($article);
                $entityManager->persist($category);
            }

            $article->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_my_articles', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('articles/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_articles_show', methods: ['GET'])]
    public function show(Articles $article): Response
    {
        return $this->render('articles/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_articles_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Articles $article, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ArticlesType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser(); // recuperation de l'utilisateur connecté
            $article->setUser($user); // on stock l'utilisateur dans l'instance $article

            // recuperation de l'image//Gestion de l'image
            $image = $form->get('image')->getData();

            //le nom d'origine de l'image
            $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);

            //sécuriser le nom de l'image
            $safeImage = $slugger->slug($originalFilename);

            //Generation d'un nom unique de l'image en ajoutant un hash aléatoire
            $newImage = $safeImage . '-' . uniqid() . '.' . $image->guessExtension();

            try {
                //Déplacement de l'image dans le dossier public/images/profils
                $image->move($this->getParameter('article_directory'), $newImage);
            } catch (FileException $th) {

                $this->addFlash('error', 'Erreur lors de l\'upload de l\'image');
            }




            //mise a jour de la propriété picture de l'entité Profil
            $article->setImage($newImage);

            //récupération de la catégorie sélectionnée dans le formulaire
            $categories = $article->getCategory()->getValues();

            foreach ($categories as $category) {

                $category->addArticle($article);
                $entityManager->persist($category);
            }

            $idArticle = $article->getId(); // recuperation de l'id de l'article modifié


            $entityManager->flush();

            return $this->redirectToRoute('app_articles_show', ['id' => $idArticle], Response::HTTP_SEE_OTHER);
        }

        return $this->render('articles/edit.html.twig', [
            'article' => $article,
            'form' => $form,

        ]);
    }

    #[Route('/{id}', name: 'app_articles_delete', methods: ['POST'])]
    public function delete(Request $request, Articles $article, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $article->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_articles_index', [], Response::HTTP_SEE_OTHER);
    }
}
