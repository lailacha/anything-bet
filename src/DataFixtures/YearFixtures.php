<?php

namespace App\DataFixtures;

use App\Entity\Year;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class YearFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $dates = ['2020-2021', '2021-2022', '2022-2023'];

        foreach ($dates as $date) {
            $object = (new Year())
                ->setDate($date)
            ;
            $manager->persist($object);
        }

        $manager->flush();
    }
}
