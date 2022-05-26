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

        $streets = [];

        while ($line = fgets($kurskStreetsFile)) {

            $street = new Street();
            $street->setName(trim($line));

            $streets[] = $street;
        }

        fclose($kurskStreetsFile);

        // replace duplications with null
        for ($i = 0; $i < count($streets); $i++) {
            for ($j = $i + 1; $j < count($streets); $j++) {

                if ($streets[$i] !== null && $streets[$j] !== null) {
                    if ($streets[$i]->getName() === $streets[$j]->getName()) {
                        $streets[$j] = null;
                    }
                }

            }
        }

        foreach ($streets as $street) {
            if ($street !== null) {

                $manager->persist($street);
            }
        }

        $manager->flush();
    }
}
