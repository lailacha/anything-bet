<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // pwd = test
        $pwd = '$2y$13$r/sNDkWI9w4h0XHSIYqYJusHu3JYZTFwEOxTCkXG31rL9Dy1Tncba';

        $object = (new User())
            ->setEmail('user@user.fr')
            ->setPassword($pwd)
            ->setPseudo('User1')
            ->setAvatar('https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mp&f=y')
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
            ->setAvatar('https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mp&f=y')
        ;
        $manager->persist($object);

        for ($i=0; $i<50; $i++) {
            $object = (new User())
                ->setEmail('user' . $i . '@user.fr')
                ->setPassword($pwd)
                ->setIsVerified(true)
                ->setRoles(['ROLE_USER'])
                ->setFirstName('User')
                ->setLastName('User')
                ->setPseudo('user' . $i)
                ->setAvatar('https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mp&f=y')
            ;
            $manager->persist($object);
        }

        $manager->flush();
    }
}
