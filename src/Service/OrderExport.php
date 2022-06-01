<?php

namespace App\Service;

use App\Entity\Order;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class OrderExport
{
    private $spreadSheet;
    private $writer;
    private $savePath;

    public function __construct(string $orderExportPath)
    {
        $this->spreadSheet = new Spreadsheet();
        $this->writer = new Xlsx($this->spreadSheet);
        $this->savePath = $orderExportPath;
    }

    public function export(Order $order): string
    {
        $cartItems = $order->getCart()->getItems();

        $workSheet = $this->spreadSheet->getActiveSheet();

        $workSheet->getCellByColumnAndRow(1,1)->setValue('Order');
        $workSheet->getCellByColumnAndRow(1,2)->setValue('Id: ' . $order->getId());
        $workSheet->getCellByColumnAndRow(1,3)->setValue('Customer: ' . $order->getCustomer());
        $workSheet->getCellByColumnAndRow(1,4)->setValue('Original filename: order_' . $order->getId() . '.xlsx');
        $workSheet->getCellByColumnAndRow(1,5)->setValue('Total price: ' . $order->getTotal());
        $workSheet->getCellByColumnAndRow(1,6)->setValue('Info: ' . $order->getInfo());
        $workSheet->getCellByColumnAndRow(1,7)->setValue('Created at:');
        $workSheet->getCellByColumnAndRow(1,8)->setValue('Updated at:');
        $workSheet->getCellByColumnAndRow(2,7)->setValue($order->getCreatedAt());
        $workSheet->getCellByColumnAndRow(2,8)->setValue($order->getUpdatedAt());
        $workSheet->getCellByColumnAndRow(1, 9)->setValue('-----------------------------');
        // CART
        $workSheet->getCellByColumnAndRow(1,10)->setValue('Cart');
        // cart header
        $headerHSize = 5; // cells
        $workSheet->getCellByColumnAndRow(1,11)->setValue('№');
        $workSheet->getCellByColumnAndRow(2,11)->setValue('Product');
        $workSheet->getCellByColumnAndRow(3,11)->setValue('Price per unit');
        $workSheet->getCellByColumnAndRow(4,11)->setValue('Quantity');
        $workSheet->getCellByColumnAndRow(5,11)->setValue('Item total price');
        $itemsStartRow = 11; // 12 actually but $i=1
        for ($itemsRow = 1; $itemsRow <= count($cartItems); $itemsRow++) {
            $workSheet->getCellByColumnAndRow(1,$itemsStartRow + $itemsRow)->setValue($itemsRow);
            $workSheet->getCellByColumnAndRow(2,$itemsStartRow + $itemsRow)->setValue($cartItems[$itemsRow - 1]->getProduct()->getName());
            $workSheet->getCellByColumnAndRow(3,$itemsStartRow + $itemsRow)->setValue($cartItems[$itemsRow - 1]->getProduct()->getPrice());
            $workSheet->getCellByColumnAndRow(4,$itemsStartRow + $itemsRow)->setValue($cartItems[$itemsRow - 1]->getQuantity());
            $workSheet->getCellByColumnAndRow(5,$itemsStartRow + $itemsRow)->setValue($cartItems[$itemsRow - 1]->getItemTotal());
        }
        $itemsFinishRow = $itemsStartRow + count($cartItems); // inclusive
        $workSheet->getCellByColumnAndRow(1, $itemsFinishRow + 1)->setValue('-----------------------------');
        $workSheet->getCellByColumnAndRow(1, $itemsFinishRow + 2)->setValue('Order total price: ');
        $workSheet->getCellByColumnAndRow(5, $itemsFinishRow + 2)->setValue($order->getCart()->getTotal());
        $workSheet->getCellByColumnAndRow(1, $itemsFinishRow + 3)->setValue('-----------------------------');
        $workSheet->getCellByColumnAndRow(1, $itemsFinishRow + 4)->setValue('Order finish date');
        $workSheet->getCellByColumnAndRow(3, $itemsFinishRow + 4)->setValue($order->getUpdatedAt());
        $workSheet->getCellByColumnAndRow(1, $itemsFinishRow + 5)->setValue('Order export date');
        $workSheet->getCellByColumnAndRow(3, $itemsFinishRow + 5)->setValue(new \DateTime());

        $fileName = 'order_' . $order->getId() . '.xlsx';
        $this->writer->save($this->savePath . $fileName);

        return $fileName;
    }
}
