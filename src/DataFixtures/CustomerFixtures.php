<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Street;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CustomerFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $streets = $manager->getRepository(Street::class)->findAll();
        shuffle($streets);

        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load("assets/customers.xlsx");

        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow(); // 297?
        $highestColumn = $worksheet->getHighestColumn(); // C?
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn); // 3?

        for ($row = 1; $row <= $highestRow; $row++) {

            $lastName = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
            $firstName = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
            $middleName = $worksheet->getCellByColumnAndRow(3, $row)->getValue();

            $customer = new Customer();
            $customer->setLastName($lastName);
            $customer->setFirstName($firstName);
            $customer->setMiddleName($middleName);

            // apartmentNumber; nullable actually for 'private house case' but this time consider 'all customers live in flats'
            $customer->setApartment(mt_rand(1, 300));
            $customer->setBuildingNumber(mt_rand(1, 500));
            $customer->setStreet($streets[mt_rand(0, 1000)]);
            // info; nullable
            $isInfo = mt_rand(0, 1);
            if ($isInfo === 1) {
                $customer->setInfo('info for customer ' . $row);
            }

            /**
             * random status
             * 0 - no change, default new
             * 1 - active
             * 2 - disabled
             */
            $rand = mt_rand(0, 2);
            switch ($rand) {
                case 1:
                    $customer->setStatus(Customer::STATUS_CUSTOMER_ACTIVE);
                    break;
                case 2:
                    $customer->setStatus(Customer::STATUS_CUSTOMER_DISABLED);
                    break;
            }

            $manager->persist($customer);
        }

        $manager->flush();
    }


    public function getDependencies()
    {
        return [
            StreetFixtures::class,
        ];
    }
}
