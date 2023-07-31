<?php

namespace App\Controller;

use App\Entity\Module;
use App\Entity\Session;
use App\Form\ModuleType;
use App\Entity\Represent;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ModuleController extends AbstractController
{
    #[Route('/module', name: 'app_module')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $modules = $entityManager->getRepository(Module::class)->findAll();
        return $this->render('module/index.html.twig', [
            'modules' => $modules,
        ]);
    }

    #[Route('/module/new', name: 'new_module')]
    #[Route('/module/{id}/edit', name: 'edit_module')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function new_edit(Module $module = null, Request $request, EntityManagerInterface $entityManager): Response
    {
        if(!$this->getUser()->isVerified()){
            return $this->redirectToRoute('app_home');
        }
        if (!$this->isGranted("ROLE_ADMIN")) {
            return $this->redirectToRoute('app_module');
        } else {
            if(!$module){
                $module = new Module();
            }
    
            $form = $this->createForm(ModuleType::class, $module);
    
            $form->handleRequest($request);
    
            if($form->isSubmitted() && $form->isValid()) {
    
                $module = $form->getData();
                //prepare PDO
                $entityManager->persist($module);
                //execute PDO
                $entityManager->flush();
    
                return $this->redirectToRoute('app_module');
    
            }
            return $this->render('module/new.html.twig', [
                'formAddModule' => $form,
                'edit' => $module->getId()
            ]);
        }
    }



    #[Route('/module/{id}/remove', name: 'remove_module')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function remove(Module $module, EntityManagerInterface $entityManager): Response
    {
        if(!$this->getUser()->isVerified()){
            return $this->redirectToRoute('app_home');
        }
        if($this->isGranted("ROLE_ADMIN")){
            $entityManager->remove($module);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_module');
    }

    /*  This method is developed to program the current module with a specified session (to add and remove)*/
    #[Route('/module/{idModule}/program/{idSession}', name: 'program_module')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[ParamConverter("module", options: ['mapping' => ["idModule" => "id"]])]
    #[ParamConverter("module", options: ['mapping' => ["idSession" => "id"]])]
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

    }


    #[Route('/module/{id}', name: 'show_module')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function show(EntityManagerInterface $entityManager, Module $module): Response
    {

        $sessionsNotProgrammed = $entityManager->getRepository(Module::class)->findSessionsNotProgrammed($module->getId());
        return $this->render('module/show.html.twig', [
            'module' => $module,
            'sessionsNotProgrammed' => $sessionsNotProgrammed
        ]);
    }
}
