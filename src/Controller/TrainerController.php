<?php

namespace App\Controller;

use App\Entity\Trainer;
use App\Form\TrainerType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TrainerController extends AbstractController
{
    #[Route('/trainer', name: 'app_trainer')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        //SELECT * FROM  trainer ORDER BY name ASC
        $trainers = $entityManager->getRepository(Trainer::class)->findBy([], ['name' => 'ASC']);
        return $this->render('trainer/index.html.twig', [
            'trainers' => $trainers
        ]);
    }

    #[Route('/trainer/new', name: 'new_trainer')]
    #[Route('/trainer/{id}/edit', name: 'edit_trainer')]
    public function new(Trainer $trainer = null, Request $request, EntityManagerInterface $entityManager): Response
    {
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

    #[Route('/trainer/{id}/remove', name: 'remove_trainer')]
    public function remove(Trainer $trainer, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($trainer);
        $entityManager->flush();
        
        return $this->redirectToRoute('app_trainer');
    }

    #[Route('/trainer/{id}', name: 'show_trainer')]
    public function show(Trainer $trainer): Response
    {
        return $this->render('trainer/show.html.twig', [
            'trainer' => $trainer
        ]);
    }
}
