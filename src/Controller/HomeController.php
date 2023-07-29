<?php

namespace App\Controller;

use DateTime;
use App\Entity\Session;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $sessions = $entityManager->getRepository(Session::class)->findSessionsArray();

        $today = new DateTime();

        foreach($sessions as $session){
            if($today > $session->getDateEnd()){
                $sessionsPast[] = $session;
            }
            elseif($today < $session->getDateStart()){
                $sessionsFuture[] = $session;
            }
            else {
                $sessionsNow[] = $session;
            }
        }


        return $this->render('home/index.html.twig', [
            'sessions' => $sessions,
            'sessionsPast' => $sessionsPast,
            'sessionsNow' => $sessionsNow,
            'sessionsFuture' => $sessionsFuture
        ]);
    }
}
