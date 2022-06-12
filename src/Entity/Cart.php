<?php

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints\Valid;

#[ORM\Entity(repositoryClass: CartRepository::class)]
class Cart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Valid]
    #[AppAssert\UniqueForCollection(options: ['fields' => 'product.id'])]
    #[ORM\OneToMany(mappedBy: 'cart', targetEntity: CartItem::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, CartItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(CartItem $item): self
    {
        /*
         * 'silent update'
         */
        /*
        foreach ($this->items as $existingItem) {
            // The item already exists, update the quantity
            if ($existingItem->equals($item)) {
                $existingItem->setQuantity(
                    $existingItem->getQuantity() + $item->getQuantity()
                );
                return $this;
            }
        }
        $this->items[] = $item;
        $item->setCart($this);
        return $this;
        */

        /*
         * 'silent fail'
         */
        /*
        foreach ($this->items as $existingItem) {
            if ($existingItem->equals($item)) {
                return false;
            }
        }
        $this->items[] = $item;
        $item->setCart($this);

        return true;
        */

        // default
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->setCart($this);
        }

        return $this;
    }

    public function removeItem(CartItem $item): self
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getCart() === $this) {
                $item->setCart(null);
            }
        }

        return $this;
    }

    /**
     * Calc the cart total
     *
     * @return float
     */
    public function getTotal(): float
    {
        $total = 0;

        foreach ($this->items as $item) {
            $total += $item->getItemTotal();
        }

        return $total;
    }

    /**
     * Removes all items from the order.
     *
     * @return $this
     */
    public function removeAllItems(): self
    {
        foreach ($this->items as $item) {
            $this->removeItem($item);
        }

        return $this;
    }
}
