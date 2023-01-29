<?php

namespace App\DataFixtures;

use App\Entity\Hackathon;
use App\Entity\User;
use App\Entity\Year;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class HackathonFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $years = $manager->getRepository(Year::class)->findAll();
        $users = $manager->getRepository(User::class)->findAll();

        for ($i=0; $i<10; $i++) {
            $object = (new Hackathon())
                ->setName($faker->name)
                ->setDescription($faker->paragraph)
                ->setStartDate($faker->dateTimeBetween('-1 years', 'now'))
                ->setYear($faker->randomElement($years))
            ;

            for ($y=0; $y<$faker->numberBetween(1, 10); $y++) {
                $object->addParticipant($faker->randomElement($users));
            }

            $manager->persist($object);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            YearFixtures::class,
            UserFixtures::class
        ];
    }
}
