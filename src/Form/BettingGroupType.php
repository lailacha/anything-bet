<?php

namespace App\Form;

use App\Entity\BettingGroup;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BettingGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('userMax')
            ->add('cover', FileType::class, [
                'mapped' => false,
                'required' => false,
                'attr' => [
                    "class" => "flex flex-column"
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            "class" => "form bet",
        ]);
    }
}
