<?php

namespace App\EntityListener;

use App\Entity\Order;
use App\Message\OrderMessage;
use Symfony\Component\Messenger\MessageBusInterface;

class OrderEntityListener
{
    private $bus;

    /**
     * @param MessageBusInterface $bus
     */
    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    public function prePersist(Order $order): void
    {
        if ($order->getTotal() != 0) {
            $order->setStatus(Order::STATUS_ORDER_IN_PROGRESS);
        }
    }

    public function postPersist(Order $order): void
    {
        if ($order->getStatus() === Order::STATUS_ORDER_IN_PROGRESS) {
            // send to queue, send telegram message
            $this->bus->dispatch(new OrderMessage($order->getId()));
        }
    }

    public function preUpdate(Order $order): void
    {
        if ($order->getTotal() != 0) {
            $order->setStatus(Order::STATUS_ORDER_IN_PROGRESS);
            // send to queue, send telegram message
            $this->bus->dispatch(new OrderMessage($order->getId()));
        }

        if ($order->getStatus() === Order::STATUS_ORDER_FINISHED) {
            // send to queue, send email, link to order, link to csv (ask for export to csv or pdf or w/e or not)
        }
    }
}
