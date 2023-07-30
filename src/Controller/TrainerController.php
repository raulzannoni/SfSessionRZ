<?php

namespace App\Controller;

use App\Entity\Trainer;
use App\Form\TrainerType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TrainerController extends AbstractController
{
    #[Route('/trainer', name: 'app_trainer')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        if(!$this->getUser()->isVerified()){
            return $this->redirectToRoute('app_home');
        }
        //SELECT * FROM  trainer ORDER BY name ASC
        $trainers = $entityManager->getRepository(Trainer::class)->findBy([], ['name' => 'ASC']);
        return $this->render('trainer/index.html.twig', [
            'trainers' => $trainers
        ]);
    }

    #[Route('/trainer/new', name: 'new_trainer')]
    #[Route('/trainer/{id}/edit', name: 'edit_trainer')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function new(Trainer $trainer = null, Request $request, EntityManagerInterface $entityManager): Response
    {
        if(!$this->getUser()->isVerified()){
            return $this->redirectToRoute('app_home');
        }
        if (!$this->isGranted("ROLE_ADMIN")) {
            return $this->redirectToRoute('app_trainer');
        } else {
            if(!$trainer){
                $trainer = new Trainer();
            }
            $form = $this->createForm(TrainerType::class, $trainer);
    
            $form->handleRequest($request);
    
            if($form->isSubmitted() && $form->isValid()) {
    
                $trainer = $form->getData();
                //prepare PDO
                $entityManager->persist($trainer);
                //execute PDO
                $entityManager->flush();
    
                return $this->redirectToRoute('app_trainer');
    
            }
    
            return $this->render('trainer/new.html.twig', [
                'formAddTrainer' => $form,
                'edit' => $trainer->getId()
            ]);
        }
    }

    #[Route('/trainer/{id}/remove', name: 'remove_trainer')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function remove(Trainer $trainer, EntityManagerInterface $entityManager): Response
    {
        if(!$this->getUser()->isVerified()){
            return $this->redirectToRoute('app_home');
        }
        if ($this->isGranted("ROLE_ADMIN")) {
            $entityManager->remove($trainer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_trainer');
    }

    #[Route('/trainer/{id}', name: 'show_trainer')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function show(Trainer $trainer): Response
    {
        if(!$this->getUser()->isVerified()){
            return $this->redirectToRoute('app_home');
        }
        return $this->render('trainer/show.html.twig', [
            'trainer' => $trainer
        ]);
    }
}
