<?php

namespace App\Form;

use App\Entity\Session;
use App\Entity\Stagiaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class StagiaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                    ]
                ])
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                    ]
                ])
            ->add('email', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                    ]
                ])
            ->add('dateBirth', DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                    ]
                ])
            //->add('sessions', EntityType::class, [
            //    'class' => Session::class
            //])
            ->add('Add', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary'
                    ]
                ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Stagiaire::class,
        ]);
    }
}
