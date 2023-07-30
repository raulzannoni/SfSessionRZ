<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\Stagiaire;
use App\Form\StagiaireType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class StagiaireController extends AbstractController
{
    #[Route('/stagiaire', name: 'app_stagiaire')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        if(!$this->getUser()->isVerified()){
            return $this->redirectToRoute('app_home');
        }
        //SELECT * FROM  stagiaire ORDER BY name ASC
        $stagiaires = $entityManager->getRepository(Stagiaire::class)->findBy([], ['name' => 'ASC']);
        return $this->render('stagiaire/index.html.twig', [
            'stagiaires' => $stagiaires
        ]);
    }

    #[Route('/stagiaire/new', name: 'new_stagiaire')]
    #[Route('/stagiaire/{id}/edit', name: 'edit_stagiaire')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function new(Stagiaire $stagiaire = null, Request $request, EntityManagerInterface $entityManager): Response
    {
        if(!$this->getUser()->isVerified()){
            return $this->redirectToRoute('app_home');
        }
        if (!$this->isGranted("ROLE_ADMIN")) {
            return $this->redirectToRoute('app_stagiaire');
        } else {
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
    }

    #[Route('/stagiaire/{id}/remove', name: 'remove_stagiaire')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function remove(Stagiaire $stagiaire, EntityManagerInterface $entityManager): Response
    {
        if(!$this->getUser()->isVerified()){
            return $this->redirectToRoute('app_home');
        }
        if($this->isGranted("ROLE_ADMIN")){
            $entityManager->remove($stagiaire);
            $entityManager->flush();
        }
        
        return $this->redirectToRoute('app_stagiaire');
    }

    /*  This method is developed to add and remove session from the current stagiaire*/
    #[Route('/stagiaire/{idStagiaire}/add_remove_Session/{idSession}', name: 'add_remove_session')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[ParamConverter("stagiaire", options: ['mapping' => ["idStagiaire" => "id"]])]
    #[ParamConverter("session", options: ['mapping' => ["idSession" => "id"]])]
    public function add_remove_session(Session $session, Stagiaire $stagiaire, EntityManagerInterface $entityManager) {
        if(!$this->getUser()->isVerified()){
            return $this->redirectToRoute('app_home');
        }
        if($this->isGranted("ROLE_ADMIN")) {
            $stagiaireSubscribed = $entityManager->getRepository(Stagiaire::class)->findStagiaireArrayInSessionId($session->getId());

            if(in_array($stagiaire, $stagiaireSubscribed)){
                $stagiaire->removeSession($session);
            }
            else{
                $stagiaire->addSession($session);
            }

            $entityManager->persist($session);
            $entityManager->persist($stagiaire);

            $entityManager->flush();
        }

        return $this->redirectToRoute('show_stagiaire', ['id' => $stagiaire->getId()]);
    } 

    
    #[Route('/stagiaire/{id}', name: 'show_stagiaire')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function show(EntityManagerInterface $entityManager,Stagiaire $stagiaire): Response
    {
        if(!$this->getUser()->isVerified()){
            return $this->redirectToRoute('app_home');
        }
        if($stagiaire){
            $sessionsNotSubscribed = $entityManager->getRepository(Stagiaire::class)->findSessionsNotSubscribed($stagiaire->getId());
        
            return $this->render('stagiaire/show.html.twig', [
                'stagiaire' => $stagiaire,
                'sessionsNotSubscribed' => $sessionsNotSubscribed
            ]);
        } else {
            return $this->redirectToRoute("app_stagiaire");
        }
    }

}