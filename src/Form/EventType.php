<?php

namespace App\Form;

use App\Entity\BettingGroup;
use App\Entity\Event;
use App\Entity\Bet;
use App\Repository\BettingGroupRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\File;

class EventType extends AbstractType
{

    private BettingGroupRepository $bettingGroupRepository;

    public function __construct(BettingGroupRepository $bettingGroupRepository)
    {
        $this->bettingGroupRepository = $bettingGroupRepository;
    }

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
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Formats autorisés : jpeg, png',
                    ])
                ],
            ])

            ->add('startAt',  DateTimeType::class, [
                'html5' => false,
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'format' => 'yyyy-MM-dd HH:mm',
                'attr' => [
                    "class" => "flex flex-column"
                ],

            ])
            ->add('finishAt',  DateTimeType::class, [
                'html5' => false,
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'format' => 'yyyy-MM-dd HH:mm',
                'attr' => [
                    "class" => "flex flex-column"
                ],

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
                ],
                'constraints' => [
                    new Count([
                        'min' => 2,
                        'minMessage' => 'Vous devez ajouter au moins {{ limit }} paris',
                    ]),
                ],
            ])

        ;

        $bettingGroupId = $options['betting_group'];
        if ($bettingGroupId !== null) {
            $bettingGroup = $this->bettingGroupRepository->find($bettingGroupId);
            $builder->add('betting_group', EntityType::class, [
                'data' => $bettingGroup,
                'class' => BettingGroup::class,
                'query_builder' => function (BettingGroupRepository $bettingGroupRepository) use ($bettingGroupId) {
                    return $bettingGroupRepository->createQueryBuilder('b')
                        ->where('b.id = :id')
                        ->setParameter('id', $bettingGroupId);
                },
            ]);
        } else {
            $builder->add('betting_group', HiddenType::class);
        }

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'attr' => [
                "class" => "",
            ],
                'betting_group' => null,
        ]);
    }
}
