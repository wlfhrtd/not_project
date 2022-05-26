<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load("assets/products.xlsx");

        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow(); // 78?
        $highestColumn = $worksheet->getHighestColumn(); // J?
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn); // 10?

        // row == 1 is header so skip
        for ($row = 2; $row <= $highestRow; $row++) {

            $productName = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
            $productDesc = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
            $productPrice = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
            $productInStock = $worksheet->getCellByColumnAndRow(7, $row)->getValue();

            $product = new Product();
            $product->setName($productName);
            $product->setDescription($productDesc);
            $product->setPrice((float)$productPrice);
            $product->setQuantityInStock((int)$productInStock);

            /**
             * random status
             * 0 - no change, default new
             * 1 - in_stock
             * 2 - out_of_stock
             * 3 - hidden (deleted)
             */
            $rand = mt_rand(0, 3);
            switch ($rand) {
                case 1:
                    $product->setStatus(Product::STATUS_PRODUCT_IN_STOCK);
                    break;
                case 2:
                    $product->setStatus(Product::STATUS_PRODUCT_OUT_OF_STOCK);
                    break;
                case 3:
                    $product->setStatus(Product::STATUS_PRODUCT_HIDDEN);
                    break;
            }

            $manager->persist($product);
        }

        $manager->flush();
    }
}
