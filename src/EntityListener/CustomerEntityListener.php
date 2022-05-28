<?php

namespace App\EntityListener;

use App\Entity\Customer;

class CustomerEntityListener
{
    public function preUpdate(Customer $customer): void
    {
        // if (something_changed) ...     right now it's enough to make any change to entity to trigger this
        $customer->setStatus(Customer::STATUS_CUSTOMER_ACTIVE);
    }
}
