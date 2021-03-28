<?php


namespace App\Job\JobData;


use Doctrine\Common\Collections\ArrayCollection;

class OrderItemJobData extends ArrayCollection
{

    public function __construct($product_id, $sku_id, $num)
    {
        parent::__construct([
            "product_id" => $product_id,
            "sku_id" => $sku_id,
            "num" => $num,
        ]);
    }

}