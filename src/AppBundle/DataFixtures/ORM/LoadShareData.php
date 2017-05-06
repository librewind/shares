<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Share;

class LoadShareData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $share = new Share();

        $share->setName('YAHOO');

        $share->setSymbol('YHOO');

        $manager->persist($share);
        $manager->flush();
    }
}