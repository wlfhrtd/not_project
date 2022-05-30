<?php

namespace App\MessageHandler;

use App\Message\OrderMessage;
use App\Notification\OrderNotification;
use App\Repository\OrderRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Notifier\NotifierInterface;

class OrderMessageHandler implements MessageHandlerInterface
{
    private $orderRepository;
    private $notifier;

    /**
     * @param OrderRepository $orderRepository
     * @param NotifierInterface $notifier
     */
    public function __construct(OrderRepository $orderRepository, NotifierInterface $notifier)
    {
        $this->orderRepository = $orderRepository;
        $this->notifier = $notifier;
    }

    public function __invoke(OrderMessage $message)
    {
        $orderId = $message->getId();
        $order = $this->orderRepository->findOneBy(['id' => $orderId]);
        $this->notifier->send(new OrderNotification($order->getId()));
    }
}
