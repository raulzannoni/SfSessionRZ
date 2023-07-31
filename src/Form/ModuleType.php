<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Module;
use App\Entity\Trainer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ModuleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                    ]
                ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('Add', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary'
                    ]
                ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Module::class,
        ]);
    }
}
