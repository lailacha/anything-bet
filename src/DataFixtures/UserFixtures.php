<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // pwd = test
        $pwd = '$2y$13$r/sNDkWI9w4h0XHSIYqYJusHu3JYZTFwEOxTCkXG31rL9Dy1Tncba';
        $faker = Factory::create('fr_FR');

        $object = (new User())
            ->setEmail('user@user.fr')
            ->setPassword($pwd)
            ->setPseudo('User1')
            ->setAvatar('default.svg')
            ->setIsVerified(true)
            ->setRoles(['ROLE_USER'])
            ->setFirstName('User')
            ->setLastName('User')
        ;
        $manager->persist($object);

        $object = (new User())
            ->setEmail('admin@user.fr')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($pwd)
            ->setIsVerified(true)
            ->setPseudo('admin')
            ->setFirstName('GroupAdministrator')
            ->setLastName('GroupAdministrator')
            ->setAvatar('default.svg')
        ;
        $manager->persist($object);

        for ($i=0; $i<50; $i++) {
            $object = (new User())
                ->setEmail('user' . $i . '@user.fr')
                ->setPassword($pwd)
                ->setIsVerified(true)
                ->setRoles(['ROLE_USER'])
                ->setFirstName($faker->firstName())
                ->setLastName($faker->lastName())
                ->setPseudo($faker->userName())
                ->setAvatar('default.svg')
            ;
            $manager->persist($object);
        }

        $manager->flush();
    }
}
