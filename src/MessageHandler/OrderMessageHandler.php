<?php

namespace App\MessageHandler;

use App\Message\OrderMessage;
use App\Notification\OrderNotification;
use App\Repository\OrderRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class OrderMessageHandler implements MessageHandlerInterface
{
    private $orderRepository;
    private $notifier;
    private $router;

    /**
     * @param OrderRepository $orderRepository
     * @param NotifierInterface $notifier
     * @param UrlGeneratorInterface $router
     */
    public function __construct(OrderRepository $orderRepository, NotifierInterface $notifier, UrlGeneratorInterface $router)
    {
        $this->orderRepository = $orderRepository;
        $this->notifier = $notifier;
        $this->router = $router;
    }

    public function __invoke(OrderMessage $message)
    {
        $orderId = $message->getId();
        $order = $this->orderRepository->findOneBy(['id' => $orderId]);
        $orderUrl = $this->router->generate('app_order_show', ['id' => $orderId], UrlGeneratorInterface::ABSOLUTE_URL);
        $this->notifier->send(new OrderNotification($order, $orderUrl), ...$this->notifier->getAdminRecipients());
    }
}
