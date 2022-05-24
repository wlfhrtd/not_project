<?php

namespace App\DataFixtures;

use App\Entity\Street;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class StreetFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $kurskStreetsFile = fopen('assets/kursk_streets.txt', 'r');

        while ($line = fgets($kurskStreetsFile)) {

            $street = new Street();
            $street->setName(trim($line, "\n"));

            $manager->persist($street);
        }

        fclose($kurskStreetsFile);

        $manager->flush();
    }
}
