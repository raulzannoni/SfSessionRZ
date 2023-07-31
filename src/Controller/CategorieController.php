<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategorieController extends AbstractController
{
    #[Route('/categorie', name: 'app_categorie')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $categories = $entityManager->getRepository(Categorie::class)->findAll();
        return $this->render('categorie/index.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/categorie/new', name: 'new_categorie')]
    #[Route('/categorie/{id}/edit', name: 'edit_categorie')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function new_edit(Categorie $categorie = null, Request $request, EntityManagerInterface $entityManager): Response
    {
        if(!$this->getUser()->isVerified()){
            return $this->redirectToRoute('app_home');
        }
        if (!$this->isGranted("ROLE_ADMIN")) {
            return $this->redirectToRoute('app_categorie');
        } else {
            if(!$categorie){
                $categorie = new Categorie();
            }
    
            $form = $this->createForm(CategorieType::class, $categorie);
    
            $form->handleRequest($request);
    
            if($form->isSubmitted() && $form->isValid()) {
    
                $session = $form->getData();
                //prepare PDO
                $entityManager->persist($session);
                //execute PDO
                $entityManager->flush();
    
                return $this->redirectToRoute('app_categorie');
    
            }
            return $this->render('categorie/new.html.twig', [
                'formAddCategorie' => $form,
                'edit' => $categorie->getId()
            ]);
        }
    }

    #[Route('/categorie/{id}/remove', name: 'remove_categorie')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function remove(Categorie $categorie, EntityManagerInterface $entityManager): Response
    {
        if(!$this->getUser()->isVerified()){
            return $this->redirectToRoute('app_home');
        }
        if($this->isGranted("ROLE_ADMIN")){
            $entityManager->remove($categorie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_categorie');
    }











    #[Route('/categorie/{id}', name: 'show_categorie')]
    public function show(Categorie $categorie): Response
    {
        return $this->render('categorie/show.html.twig', [
            'categorie' => $categorie
        ]);
    }
}
