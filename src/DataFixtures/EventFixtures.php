<?php

namespace App\DataFixtures;

use App\Entity\Event;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;


class EventFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $user = $manager->getRepository(User::class)->findAll();

        for ($i=0; $i<10; $i++) {
            $object = (new Event())
                ->setTheUser($faker->randomElement($user))
                ->setName($faker->sentence(10, true))
                ->setStartAt(new \DateTimeImmutable())
                ->setFinishAt(new \DateTimeImmutable('2023-12-31 23:59:59'))
                ->setCreatedAt(new \DateTimeImmutable())
                ->setResult('Result 1')
            ;
            $manager->persist($object);
        }


        $manager->flush();
    }
    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}


