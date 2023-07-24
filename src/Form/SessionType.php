<?php

namespace App\Form;

use App\Entity\Session;
use App\Entity\Formation;
use App\Entity\Stagiaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class SessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                    ]
                ])
            ->add('totalPlaces', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control'
                    ]
                ])
            ->add('dateStart', DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                    ]
                ])
            ->add('dateEnd', DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                    ]
                ])
            ->add('program', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control'
                    ]
                ])
            ->add('formation', EntityType::class, [
                'class' => Formation::class,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            //->add('stagiaires', EntityType::class, [
            //    'class' => Stagiaire::class
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
            'data_class' => Session::class,
        ]);
    }
}
