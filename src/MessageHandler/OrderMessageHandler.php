<?php

namespace App\MessageHandler;

use App\Message\OrderMessage;
use App\Notification\OrderNotification;
use App\Repository\OrderRepository;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Notifier\NotifierInterface;

class OrderMessageHandler implements MessageHandlerInterface
{
    private $orderRepository;
    private $notifier;
    private $mailer;
    private $adminEmail;

    /**
     * @param OrderRepository $orderRepository
     * @param NotifierInterface $notifier
     * @param MailerInterface $mailer
     * @param string $adminEmail
     */
    public function __construct(OrderRepository $orderRepository, NotifierInterface $notifier, MailerInterface $mailer, string $adminEmail)
    {
        $this->orderRepository = $orderRepository;
        $this->notifier = $notifier;
        $this->mailer = $mailer;
        $this->adminEmail = $adminEmail;
    }

    public function __invoke(OrderMessage $message)
    {
        $orderId = $message->getId();
        $order = $this->orderRepository->findOneBy(['id' => $orderId]);
        $this->notifier->send(new OrderNotification($order->getId()));
        $this->mailer->send((new NotificationEmail())
            ->subject('New order added')
            ->htmlTemplate('emails/order_notification.html.twig')
            ->from($this->adminEmail)
            ->to($this->adminEmail)
            ->context(['order' => $order])
        );
    }
}
