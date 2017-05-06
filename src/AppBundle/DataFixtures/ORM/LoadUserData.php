<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\User;

class LoadUserData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $user = new User();

        $user->setUsername('user1');

        $user->setEmail('user1@test.local');

        $user->setPlainPassword('123456');

        $user->setEnabled(true);

        $manager->persist($user);
        $manager->flush();
    }
}