<?php

namespace App\Controller;

use App\Entity\Profils;
use App\Form\ProfilsType;
use App\Repository\CategoriesRepository;
use App\Repository\ProfilsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/account')]
final class ProfilsController extends AbstractController
{
    #[Route(name: 'app_profils_index', methods: ['GET'])]
    public function index(ProfilsRepository $profilsRepository, CategoriesRepository $categoriesRepository): Response ///////////
    {

        $user = $this->getUser();
        $categories = $categoriesRepository->findAll(); ////////
        return $this->render('account/account.html.twig', [
            'profil' => $profilsRepository->findByUser($user),
            'categories' => $categories, ////////

        ]);
    }

    #[Route('/new', name: 'app_profils_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, CategoriesRepository $categoriesRepository): Response
    {
        $profil = new Profils();
        $form = $this->createForm(ProfilsType::class, $profil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->getUser();
            $profil->setUser($user);

            // gestion de l'image uploadée

            $picture = $form->get('picture')->getData();
            //le nom d'origine de l'image 
            $originalPicture = pathinfo($picture->getClientOriginalName(), PATHINFO_FILENAME);
            // Sécurisation du nom du fichier
            $safePicture = $slugger->slug($originalPicture);


            // Génération d'un nom unique pour l'image en ajoutant un identifiant unique
            $newPicture = $safePicture . '-' . uniqid() . '.' . $picture->guessExtension();

            $picture->move($this->getParameter('profil_directory'), $newPicture);

            $profil->setPicture($newPicture); // Mise à jour de la propriété picture dans $profil (object) avec le nouveau nom du fichier

            $entityManager->persist($profil);
            $entityManager->flush();

            return $this->redirectToRoute('app_profils_index', [], Response::HTTP_SEE_OTHER);
        }

        $categories = $categoriesRepository->findAll(); ////////

        return $this->render('profils/new.html.twig', [
            'profil' => $profil,
            'form' => $form,
            'categories' => $categories, ////////
        ]);
    }

    // #[Route('/{id}', name: 'app_profils_show', methods: ['GET'])]
    // public function show(Profils $profil): Response
    // {
    //     return $this->render('profils/show.html.twig', [
    //         'profil' => $profil,
    //     ]);
    // }

    #[Route('/{id}/edit', name: 'app_profils_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Profils $profil, EntityManagerInterface $entityManager, SluggerInterface $slugger, CategoriesRepository $categoriesRepository): Response
    {

        if ($profil->getId() !== $this->getUser()->getProfil()->getId()) {

            return $this->redirectToRoute('app_profils_edit', ['id' => $this->getUser()->getProfil()->getId()], Response::HTTP_SEE_OTHER);
        }

        $form = $this->createForm(ProfilsType::class, $profil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->getUser();
            $profil->setUser($user);

            // gestion de l'image uploadée

            $picture = $form->get('picture')->getData();
            //le nom d'origine de l'image 
            $originalPicture = pathinfo($picture->getClientOriginalName(), PATHINFO_FILENAME);
            // Sécurisation du nom du fichier
            $safePicture = $slugger->slug($originalPicture);


            // Génération d'un nom unique pour l'image en ajoutant un identifiant unique
            $newPicture = $safePicture . '-' . uniqid() . '.' . $picture->guessExtension();

            $picture->move($this->getParameter('profil_directory'), $newPicture);

            $profil->setPicture($newPicture); // Mise à jour de la propriété picture dans $profil (object) avec le nouveau nom du fichier
            $entityManager->flush();

            return $this->redirectToRoute('app_profils_index', [], Response::HTTP_SEE_OTHER);
        }
        $categories = $categoriesRepository->findAll(); ////////
        return $this->render('profils/edit.html.twig', [
            'profil' => $profil,
            'form' => $form,
            'categories' => $categories, ////////
        ]);
    }

    #[Route('/{id}', name: 'app_profils_delete', methods: ['POST'])]
    public function delete(Request $request, Profils $profil, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $profil->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($profil);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_profils_index', [], Response::HTTP_SEE_OTHER);
    }
}
