<?php

namespace App\EntityListener;

use App\Entity\Order;

class OrderEntityListener
{
    public function postFlush(Order $order): void
    {
        if ($order->getStatus() === Order::STATUS_ORDER_IN_PROGRESS) {
            // send to queue, send telegram message
        }

        if ($order->getStatus() === Order::STATUS_ORDER_FINISHED) {
            // send to queue, send email, link to order, link to csv (ask for export to csv or pdf or w/e or not)
        }
    }
}
