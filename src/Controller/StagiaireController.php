<?php

namespace App\Controller;

use App\Entity\Stagiaire;
use App\Form\StagiaireType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StagiaireController extends AbstractController
{
    #[Route('/stagiaire', name: 'app_stagiaire')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        //SELECT * FROM  stagiaire ORDER BY name ASC
        $stagiaires = $entityManager->getRepository(Stagiaire::class)->findBy([], ['name' => 'ASC']);
        return $this->render('stagiaire/index.html.twig', [
            'stagiaires' => $stagiaires
        ]);
    }

    #[Route('/stagiaire/new', name: 'new_stagiaire')]
    #[Route('/stagiaire/{id}/edit', name: 'edit_stagiaire')]
    public function new(Stagiaire $stagiaire = null, Request $request, EntityManagerInterface $entityManager): Response
    {
        if(!$stagiaire){
            $stagiaire = new Stagiaire();
        }
        $form = $this->createForm(StagiaireType::class, $stagiaire);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $stagiaire = $form->getData();
            //prepare PDO
            $entityManager->persist($stagiaire);
            //execute PDO
            $entityManager->flush();

            return $this->redirectToRoute('app_stagiaire');

        }

        return $this->render('stagiaire/new.html.twig', [
            'formAddStagiaire' => $form,
            'edit' => $stagiaire->getId()
        ]);

    }

    #[Route('/stagiaire/{id}/remove', name: 'remove_stagiaire')]
    public function remove(Stagiaire $stagiaire, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($stagiaire);
        $entityManager->flush();
        
        return $this->redirectToRoute('app_stagiaire');
    }
    
    #[Route('/stagiaire/{id}', name: 'show_stagiaire')]
    public function show(Stagiaire $stagiaire): Response
    {
        return $this->render('stagiaire/show.html.twig', [
            'stagiaire' => $stagiaire
        ]);
    }

}