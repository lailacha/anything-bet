<?php

namespace App\DataFixtures;

use App\Entity\Bet;
use App\Entity\Event;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class BetFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $event = $manager->getRepository(Event::class)->findAll();

        for ($i=0; $i<10; $i++) {
            $object = (new Bet())
                ->setLabel($faker->city())
                ->setIdEvent($faker->randomElement($event))
            ;
            $manager->persist($object);
        }

        $manager->flush();
    }
    public function getDependencies()
    {
        return [
            EventFixtures::class,
        ];
    }
}