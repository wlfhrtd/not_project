<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    // createdAt, updatedAt
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    const STATUS_ORDER_DRAFT = 'draft';
    const STATUS_ORDER_IN_PROGRESS = 'in_progress';
    const STATUS_ORDER_FINISHED = 'finished';
    const STATUS_ORDER_CANCELED = 'canceled';

    #[ORM\Column(type: 'string', length: 255)]
    private $status;

    #[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private $customer;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $spreadsheetFilename;

    #[ORM\Column(type: 'float')]
    #[PositiveOrZero]
    private $total;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $info;

    #[ORM\ManyToOne(targetEntity: Cart::class, cascade: ['persist'])]
    private $cart;

    public function __construct()
    {
        $this->status = self::STATUS_ORDER_DRAFT;
        $this->total = 0.0;
    }

    public function __toString()
    {
        return 'Order id: ' . $this->getId() . '; order.cart id: ' . $this->getCart()->getId();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getSpreadsheetFilename(): ?string
    {
        return $this->spreadsheetFilename;
    }

    public function setSpreadsheetFilename(?string $spreadsheetFilename): self
    {
        $this->spreadsheetFilename = $spreadsheetFilename;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getInfo(): ?string
    {
        return $this->info;
    }

    public function setInfo(?string $info): self
    {
        $this->info = $info;

        return $this;
    }

    public function getCart(): ?Cart
    {
        return $this->cart;
    }

    public function setCart(?Cart $cart): self
    {
        $this->cart = $cart;

        return $this;
    }
}
