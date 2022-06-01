<?php

namespace App\Entity;

use App\Repository\CartItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

#[ORM\Entity(repositoryClass: CartItemRepository::class)]
class CartItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    #[PositiveOrZero]
    private $quantity;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $product;

    #[ORM\ManyToOne(targetEntity: Cart::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private $cart;

    public function __construct()
    {
        $this->quantity = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

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

    /**
     * Tests if given item corresponds to same cart item
     *
     * @param CartItem $item
     * @return bool
     */
    public function equals(CartItem $item): bool
    {
        return $this->getProduct()->getId() === $item->getProduct()->getId();
    }

    /**
     * Calc the item total
     *
     * @return float
     */
    public function getItemTotal(): float
    {
        return $this->getProduct()->getPrice() * $this->getQuantity();
    }
}
