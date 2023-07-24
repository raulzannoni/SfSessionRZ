<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\Stagiaire;
use App\Form\SessionType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SessionController extends AbstractController
{
    #[Route('/session', name: 'app_session')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $sessions = $entityManager->getRepository(Session::class)->findAll();
        return $this->render('session/index.html.twig', [
            'sessions' => $sessions
        ]);
    }
    
    #[Route('/session/new', name: 'new_session')]
    #[Route('/session/{id}/edit', name: 'edit_session')]
    public function new_edit(Session $session = null, Request $request, EntityManagerInterface $entityManager): Response
    {
        if(!$session){
            $session = new Session();
        }

        $form = $this->createForm(SessionType::class, $session);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $session = $form->getData();
            //prepare PDO
            $entityManager->persist($session);
            //execute PDO
            $entityManager->flush();

            return $this->redirectToRoute('app_session');

        }
        return $this->render('session/new.html.twig', [
            'formAddSession' => $form,
            'edit' => $session->getId()
        ]);
    }

    #[Route('/session/{id}/remove', name: 'remove_session')]
    public function remove(Session $session, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($session);
        $entityManager->flush();
        
        return $this->redirectToRoute('app_session');
    }
    

    #[Route('/session/{id}', name: 'show_session')]
    public function show(EntityManagerInterface $entityManager, Session $session): Response
    {
        $stagiairesNotSubscribed = $entityManager->getRepository(Session::class)->findNotSubscribed($session->getId());
        $module = $entityManager->getRepository(Session::class)->findModuleBySessionId($session->getId());
        $categorie = $entityManager->getRepository(Session::class)->findCategoryBySessionId($session->getId());

        return $this->render('session/show.html.twig', [
            'session' => $session,
            'stagiairesNotSubscribed' => $stagiairesNotSubscribed,
            'module' => $module,
            'categorie' => $categorie
        ]);
    }
}
