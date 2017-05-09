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

        $share = new Share();

        $share->setName('Apple Inc.');

        $share->setSymbol('AAPL');

        $manager->persist($share);
        $manager->flush();

        $share = new Share();

        $share->setName('Hewlett-Packard');

        $share->setSymbol('HPQ');

        $manager->persist($share);
        $manager->flush();

        $share = new Share();

        $share->setName('Intel');

        $share->setSymbol('INTC');

        $manager->persist($share);
        $manager->flush();

        $share = new Share();

        $share->setName('The Coca-Cola Company');

        $share->setSymbol('KO');

        $manager->persist($share);
        $manager->flush();

        $share = new Share();

        $share->setName('Microsoft');

        $share->setSymbol('MSFT');

        $manager->persist($share);
        $manager->flush();

        $share = new Share();

        $share->setName('AT&T');

        $share->setSymbol('T');

        $manager->persist($share);
        $manager->flush();

        $share = new Share();

        $share->setName('Texas Instruments');

        $share->setSymbol('TXN');

        $manager->persist($share);
        $manager->flush();

        $share = new Share();

        $share->setName('Walmart');

        $share->setSymbol('WMT');

        $manager->persist($share);
        $manager->flush();
    }
}