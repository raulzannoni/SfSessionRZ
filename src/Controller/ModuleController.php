<?php

namespace App\Controller;

use App\Entity\Module;
use App\Entity\Session;
use App\Form\ModuleType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
