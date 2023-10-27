<?php

namespace App\Util\Amazon;

use AmazonPHP\SellingPartner\Model\Orders\ItemApprovalStatus;
use AmazonPHP\SellingPartner\Model\Orders\ItemApprovalType;

class OrderItemCreator implements CreatorInterface
{
    /**
     * @var string[]
     */
    public array $amazon_order_ids;

    public function setAmazonOrderIds(array $amazon_order_ids): void
    {
        $this->amazon_order_ids = $amazon_order_ids;
    }

    public function getAmazonOrderIds(): array
    {
        return $this->amazon_order_ids;
    }
}