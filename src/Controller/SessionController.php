<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Module;
use App\Entity\Session;
use App\Entity\Represent;
use App\Entity\Stagiaire;
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


    /*  This method is developed to program the current session in a specified module (to add and remove)*/
    #[Route('/session/{idSession}/program/{idModule}', name: 'program_session')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[ParamConverter("session", options: ['mapping' => ["idSession" => "id"]])]
    #[ParamConverter("module", options: ['mapping' => ["idModule" => "id"]])]
    public function program(Session $session, Module $module, EntityManagerInterface $entityManager, Request $request) {
        
        if(!$this->getUser()->isVerified()){
            return $this->redirectToRoute('app_home');
        }
        if($this->isGranted("ROLE_ADMIN")){

            $sessionsNotProgrammed = $entityManager->getRepository(Module::class)->findSessionsNotProgrammed($module->getId());
            
            if(in_array($session, $sessionsNotProgrammed)){
                
                $days = $request->get('days'); 
                
                $represent = new Represent();
                $represent->setDays($days);
                $session->addRepresent($represent);
                $module->addRepresent($represent);
                $entityManager->persist($represent);
                $entityManager->flush();
                
            } else{
                $representSession = iterator_to_array($session->getRepresents());
                
                $representModule = iterator_to_array($module->getRepresents());

                $represent = new Represent();

                foreach($representModule as $representM){
                    foreach($representSession as $representS ) {
                        if($representS == $representM){
                            $represent = $representS;
                            break;
                        }
                    }
                }
                //dd($represent);
                $entityManager->remove($represent);
                $entityManager->flush();
            }
            
            $entityManager->persist($session);
            $entityManager->persist($module);

            $entityManager->flush();
        }
        
        return $this->redirectToRoute('show_session', ['id' => $session->getId()]);
    }


    



    

    #[Route('/session/{id}', name: 'show_session')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function show(EntityManagerInterface $entityManager, Session $session, Request $request): Response
    {
        if(!$this->getUser()->isVerified()){
            return $this->redirectToRoute('app_home');
        }
        if($session){

            $modulesNotProgrammed = $entityManager->getRepository(Session::class)->findModulesNotProgrammed($session->getId());
            $stagiairesNotSubscribed = $entityManager->getRepository(Session::class)->findStagiairesNotSubscribed($session->getId());
    
            $totalDays = 0;
    
            foreach($session->getRepresents() as $represent) {
                $totalDays += $represent->getDays();
            }



    
            return $this->render('session/show.html.twig', [
                'session' => $session,
                'stagiairesNotSubscribed' => $stagiairesNotSubscribed,
                'modulesNotProgrammed' => $modulesNotProgrammed,
                'totalDays' => $totalDays
            ]);
        } else {
            return $this->redirectToRoute("app_session");
        }
    }
}
