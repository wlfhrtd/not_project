<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

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

    #[PositiveOrZero]
    #[ORM\Column(type: 'float')]
    private $total;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $info;

    #[Valid]
    #[ORM\ManyToOne(targetEntity: Cart::class, cascade: ['persist'])]
    private $cart;

    #[ORM\Version]
    #[ORM\Column(type: 'integer')]
    private $version;

    public function getCurrentVersion()
    {
        return $this->version;
    }

    public function __construct()
    {
        $this->status = self::STATUS_ORDER_DRAFT;
        $this->total = 0.0;
    }

    public function __toString()
    {
        return $this->id;
    }

    public function toLongString(): string
    {
        $string = "Order id: " . $this->id
            . ";\nOrder status: " . $this->status
            . ";\nOrder customer: " . $this->customer->toLongString()
            . ";\nOrder total: " . $this->total
            . ";\nOrder info: " . $this->info
            . ";\nOrder Cart id: " . $this->cart->getId()
        ;
        foreach ($this->cart->getItems() as $k => $item) {
            $string .= "\n" . $k . " => " . $item->getProduct()->getName() . "; Quantity: " . $item->getQuantity() . "; UnitsInStock: " . $item->getProduct()->getQuantityInStock() . "; Price: " . $item->getProduct()->getPrice() . "; ItemTotalPrice: " . $item->getItemTotal();
        }

        return $string;
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

    #[Callback]
    public function validate(ExecutionContextInterface $context): void
    {
        //dd($this->total, $this->getCart()->getTotal()); 362.91999999999996
        $fromForm = $this->total;
        $fromBackend = $this->getCart()->getTotal();
        $fromFormRounded = round($fromForm, 2, PHP_ROUND_HALF_UP);
        $fromBackendRounded = round($fromBackend, 2, PHP_ROUND_HALF_UP);
        if ($fromFormRounded !== $fromBackendRounded) {
            $errorMessage = 'Form total price: ' . $this->total . '; Backend total price: ' . $this->getCart()->getTotal();
            $context->buildViolation('IdenticalTo violation @@ ' . $errorMessage)
                ->atPath('total')
                ->addViolation()
            ;
        }
    }
}
