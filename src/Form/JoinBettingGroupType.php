<?php

namespace App\Form;

use App\Entity\BettingGroup;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class JoinBettingGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code',TextType::class, [
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter the code',
                    ]),
                    new Length([
                        'min' => 23,
                        'minMessage' => 'The code should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 23,
                    ]),
                ],
            ])

        ;
    }
}