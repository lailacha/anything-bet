<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Bet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'attr' => [
                    "class" => "flex flex-column"
                ]
            ])
            ->add('coverImage', FileType::class, [
                'mapped' => false,
                'required' => false,
                'attr' => [
                    "class" => "flex flex-column"
                ]
            ])

            ->add('startAt',  DateTimeType::class, [
                'html5' => false,
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'format' => 'yyyy-MM-dd HH:mm',
                'attr' => [
                    "class" => "flex flex-column"
                ]
            ])
            ->add('finishAt',  DateTimeType::class, [
                'html5' => false,
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'format' => 'yyyy-MM-dd HH:mm',
                'attr' => [
                    "class" => "flex flex-column"
                ]
            ])
            ->add('bets', CollectionType::class, [
                'entry_type' => BetType::class,
                'entry_options' => [
                    'attr' => [
                        "class" => "flex flex-column"
                    ]
                ],
                'prototype_options' => [
                    'attr' => [
                        "class" => "flex flex-col custom-flex-wrapper"
                    ]
                ],


                'allow_add' => true,
                'by_reference' => false,
                'attr' => [
                    "class" => "flex flex-col"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'attr' => [
                "class" => "form bet",
            ]
        ]);
    }
}
