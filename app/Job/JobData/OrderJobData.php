<?php

declare(strict_types=1);

namespace App\Job\JobData;

use Doctrine\Common\Collections\ArrayCollection;

class OrderJobData extends ArrayCollection
{
    public function __construct($products, $coupon_id, $address_id)
    {
        parent::__construct([
            'products' => $products,
            'coupon_id' => $coupon_id,
            'address_id' => $address_id,
            'create_date' => date('Y-m-d H:i:s'),
        ]);
    }
}
