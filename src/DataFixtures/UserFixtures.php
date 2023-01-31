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
            ->setEmail('Administrator@user.fr')
            ->setPseudo('Administrator1')
            ->setFirstname('Administrator')
            ->setLastname('Administrator')
            ->setIsVerified(true)
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($pwd);
        $manager->persist($object);


        $object = (new User())
            ->setEmail('user@user.fr')
            ->setIsVerified(true)
            ->setFirstname('User')
            ->setLastname('User')
            ->setPseudo('User1')
            ->setRoles([])
            ->setPassword($pwd)
        ;
        $manager->persist($object);

        $object = (new User())
            ->setEmail('GroupAdministrator@user.fr')
            ->setPseudo('GroupAdministrator1')
            ->setFirstname('GroupAdministrator')
            ->setLastname('GroupAdministrator')
            ->setIsVerified(true)
            ->setRoles(['ROLE_GROUP_ADMIN'])
            ->setPassword($pwd)
        ;
        $manager->persist($object);


/*        $object = (new User())
            ->setEmail('client@user.fr')
            ->setRoles(['ROLE_CLIENT'])
            ->setPassword($pwd)
        ;
        $manager->persist($object);

        $object = (new User())
            ->setEmail('admin@user.fr')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($pwd)
        ;
        $manager->persist($object);
*/
        for ($i=0; $i<50; $i++) {
            $object = (new User())
                ->setEmail('user' . $i . '@user.fr')
                ->setRoles([])
                ->setIsVerified(true)
                ->setFirstname('User' . $i)
                ->setLastname('User' . $i)
                ->setPseudo('User' . $i)
                ->setPassword($pwd)
            ;
            $manager->persist($object);
        }

        $manager->flush();
    }
}
