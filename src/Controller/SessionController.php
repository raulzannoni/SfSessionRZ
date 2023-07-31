<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\Stagiaire;
use App\Entity\User;
use App\Form\SessionType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Driver\Mysqli\Initializer\Options;
use Symfony\Component\Config\Loader\ParamConfigurator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class SessionController extends AbstractController
{
    #[Route('/session', name: 'app_session')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        if(!$this->getUser()->isVerified()){
            return $this->redirectToRoute('app_home');
        }
        $sessions = $entityManager->getRepository(Session::class)->findAll();
        return $this->render('session/index.html.twig', [
            'sessions' => $sessions
        ]);
    }
    
    #[Route('/session/new', name: 'new_session')]
    #[Route('/session/{id}/edit', name: 'edit_session')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function new_edit(Session $session = null, Request $request, EntityManagerInterface $entityManager): Response
    {
        if(!$this->getUser()->isVerified()){
            return $this->redirectToRoute('app_home');
        }
        if (!$this->isGranted("ROLE_ADMIN")) {
            return $this->redirectToRoute('app_session');
        } else {
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
    }


    /*  This method is developed to add and remove stagiaires from the current session */
    #[Route('/session/{idSession}/add_remove_Stagiaire/{idStagiaire}', name: 'add_remove_stagiaire')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[ParamConverter("session", options: ['mapping' => ["idSession" => "id"]])]
    #[ParamConverter("stagiaire", options: ['mapping' => ["idStagiaire" => "id"]])]
    public function add_remove_stagiaire(Session $session, Stagiaire $stagiaire, EntityManagerInterface $entityManager) {
        
        if(!$this->getUser()->isVerified()){
            return $this->redirectToRoute('app_home');
        }
        if($this->isGranted("ROLE_ADMIN")){

            $stagiaireSubscribed = $entityManager->getRepository(Stagiaire::class)->findStagiaireArrayInSessionId($session->getId());
            
            if(in_array($stagiaire, $stagiaireSubscribed)){
              
                $session->removeStagiaire($stagiaire);
            }
            else{
                if($session->getTotalPlaces() >  sizeof($stagiaireSubscribed)){
                    $session->addStagiaire($stagiaire);
                }
            }

            $entityManager->persist($session);
            $entityManager->persist($stagiaire);

            $entityManager->flush();
        }
        
        return $this->redirectToRoute('show_session', ['id' => $session->getId()]);
    }

    


    #[Route('/session/{id}/remove', name: 'remove_session')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function remove(Session $session, EntityManagerInterface $entityManager): Response
    {
        if(!$this->getUser()->isVerified()){
            return $this->redirectToRoute('app_home');
        }
        if($this->isGranted("ROLE_ADMIN")){
            $entityManager->remove($session);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_session');
    }
    

    #[Route('/session/{id}', name: 'show_session')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function show(EntityManagerInterface $entityManager, Session $session): Response
    {
        if(!$this->getUser()->isVerified()){
            return $this->redirectToRoute('app_home');
        }
        if($session){
            $stagiairesNotSubscribed = $entityManager->getRepository(Session::class)->findStagiairesNotSubscribed($session->getId());
            $module = $entityManager->getRepository(Session::class)->findModuleBySessionId($session->getId());
            $categorie = $entityManager->getRepository(Session::class)->findCategoryBySessionId($session->getId());
    
            $totalDays = 0;
    
            foreach($session->getRepresents() as $represent) {
                $totalDays += $represent->getDays();
            }
    
            return $this->render('session/show.html.twig', [
                'session' => $session,
                'stagiairesNotSubscribed' => $stagiairesNotSubscribed,
                'module' => $module,
                'categorie' => $categorie,
                'totalDays' => $totalDays
            ]);
        } else {
            return $this->redirectToRoute("app_session");
        }
    }
}
