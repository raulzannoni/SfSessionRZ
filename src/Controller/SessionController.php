<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\Stagiaire;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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

    #[Route('/session/{id}', name: 'show_session')]
    public function show(EntityManagerInterface $entityManager, Session $session): Response
    {
        $stagiairesNotSubscribed = $entityManager->getRepository(Session::class)->findNotSubscribed($session->getId());
        return $this->render('session/show.html.twig', [
            'session' => $session,
            'stagiairesNotSubscribed' => $stagiairesNotSubscribed
        ]);
    }
}
