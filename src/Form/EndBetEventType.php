<?php

namespace App\Form;

use App\Entity\Bet;
use App\Entity\Event;
use App\Repository\BetRepository;
use App\Repository\EventRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EndBetEventType extends AbstractType
{

    private EventRepository $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $eventId = $options['event_id'];
        $event = $this->eventRepository->find($eventId);

        $builder->add('bets', EntityType::class, [
            'class' => Bet::class,
            'query_builder' => function (BetRepository $betRepository) use ($event) {
                return $betRepository->createQueryBuilder('b')
                    ->where('b.event = :event')
                    ->setParameter('event', $event);
            },
            'choice_label' => 'label',
            'multiple' => true,
            'expanded' => true,
            'attr' => [
                'class' => 'flex flex-column'
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'event_id' => null,
        ]);
    }
}
