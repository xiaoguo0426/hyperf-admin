<?php

namespace App\Util\Amazon;

use AmazonPHP\SellingPartner\Model\Orders\ItemApprovalStatus;
use AmazonPHP\SellingPartner\Model\Orders\ItemApprovalType;

class OrderCreator implements CreatorInterface
{
    /**
     * @var string[]
     */
    public array $marketplace_ids = [];
    /**
     * @var string|null
     */
    public ?string $created_after = null;
    /**
     * @var string|null
     */
    public ?string $created_before = null;
    /**
     * @var string|null
     */
    public ?string $last_updated_after = null;
    /**
     * @var string|null
     */
    public ?string $last_updated_before = null;
    /**
     * @var string[]|null
     */
    public ?array $order_statuses = null;
    /**
     * @var string|null
     */
    public ?string $fulfillment_channels = null;
    /**
     * @var string[]|null
     */
    public ?array $payment_methods = null;
    /**
     * @var string|null
     */
    public ?string $buyer_email = null;
    /**
     * @var string|null
     */
    public ?string $seller_order_id = null;
    /**
     * @var int
     */
    public int $max_results_per_page = 100;
    /**
     * @var array|null
     */
    public ?array $easy_ship_shipment_statuses = null;
    /**
     * @var string[]|null
     */
    public ?array $electronic_invoice_statuses = null;
    /**
     * @var string|null
     */
    public ?string $next_token = null;
    /**
     * @var string[]|null
     */
    public ?array $amazon_order_ids = null;
    /**
     * @var string[]|null
     */
    public ?array $actual_fulfillment_supply_source_id = null;
    /**
     * @var bool|null
     */
    public ?bool $is_ispu = null;

    /**
     * @var string|null
     */
    public ?string $store_chain_store_id = null;
    /**
     *
     * @var ItemApprovalType[]|null
     */
    public ?array $item_approval_types = null;
    /**
     * @var ?ItemApprovalStatus[]|null
     */
    public ?array $item_approval_status = null;

    /**
     * @return array
     */
    public function getMarketplaceIds(): array
    {
        return $this->marketplace_ids;
    }

    /**
     * @param array $marketplace_ids
     * @return OrderCreator
     */
    public function setMarketplaceIds(array $marketplace_ids): OrderCreator
    {
        $this->marketplace_ids = $marketplace_ids;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCreatedAfter(): ?string
    {
        return $this->created_after;
    }

    /**
     * @param string|null $created_after
     * @return OrderCreator
     */
    public function setCreatedAfter(?string $created_after): OrderCreator
    {
        $this->created_after = $created_after;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCreatedBefore(): ?string
    {
        return $this->created_before;
    }

    /**
     * @param string|null $created_before
     * @return OrderCreator
     */
    public function setCreatedBefore(?string $created_before): OrderCreator
    {
        $this->created_before = $created_before;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastUpdatedAfter(): ?string
    {
        return $this->last_updated_after;
    }

    /**
     * @param string|null $last_updated_after
     * @return OrderCreator
     */
    public function setLastUpdatedAfter(?string $last_updated_after): OrderCreator
    {
        $this->last_updated_after = $last_updated_after;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastUpdatedBefore(): ?string
    {
        return $this->last_updated_before;
    }

    /**
     * @param string|null $last_updated_before
     * @return OrderCreator
     */
    public function setLastUpdatedBefore(?string $last_updated_before): OrderCreator
    {
        $this->last_updated_before = $last_updated_before;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getOrderStatuses(): ?array
    {
        return $this->order_statuses;
    }

    /**
     * @param array|null $order_statuses
     * @return OrderCreator
     */
    public function setOrderStatuses(?array $order_statuses): OrderCreator
    {
        $this->order_statuses = $order_statuses;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFulfillmentChannels(): ?string
    {
        return $this->fulfillment_channels;
    }

    /**
     * @param string|null $fulfillment_channels
     * @return OrderCreator
     */
    public function setFulfillmentChannels(?string $fulfillment_channels): OrderCreator
    {
        $this->fulfillment_channels = $fulfillment_channels;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getPaymentMethods(): ?array
    {
        return $this->payment_methods;
    }

    /**
     * @param array|null $payment_methods
     * @return OrderCreator
     */
    public function setPaymentMethods(?array $payment_methods): OrderCreator
    {
        $this->payment_methods = $payment_methods;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getBuyerEmail(): ?string
    {
        return $this->buyer_email;
    }

    /**
     * @param string|null $buyer_email
     * @return OrderCreator
     */
    public function setBuyerEmail(?string $buyer_email): OrderCreator
    {
        $this->buyer_email = $buyer_email;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSellerOrderId(): ?string
    {
        return $this->seller_order_id;
    }

    /**
     * @param string|null $seller_order_id
     * @return OrderCreator
     */
    public function setSellerOrderId(?string $seller_order_id): OrderCreator
    {
        $this->seller_order_id = $seller_order_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxResultsPerPage(): int
    {
        return $this->max_results_per_page;
    }

    /**
     * @param int $max_results_per_page
     * @return OrderCreator
     */
    public function setMaxResultsPerPage(int $max_results_per_page): OrderCreator
    {
        $this->max_results_per_page = $max_results_per_page;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getEasyShipShipmentStatuses(): ?array
    {
        return $this->easy_ship_shipment_statuses;
    }

    /**
     * @param array|null $easy_ship_shipment_statuses
     * @return OrderCreator
     */
    public function setEasyShipShipmentStatuses(?array $easy_ship_shipment_statuses): OrderCreator
    {
        $this->easy_ship_shipment_statuses = $easy_ship_shipment_statuses;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getElectronicInvoiceStatuses(): ?array
    {
        return $this->electronic_invoice_statuses;
    }

    /**
     * @param array|null $electronic_invoice_statuses
     * @return OrderCreator
     */
    public function setElectronicInvoiceStatuses(?array $electronic_invoice_statuses): OrderCreator
    {
        $this->electronic_invoice_statuses = $electronic_invoice_statuses;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getNextToken(): ?string
    {
        return $this->next_token;
    }

    /**
     * @param string|null $next_token
     * @return OrderCreator
     */
    public function setNextToken(?string $next_token): OrderCreator
    {
        $this->next_token = $next_token;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getAmazonOrderIds(): ?array
    {
        return $this->amazon_order_ids;
    }

    /**
     * @param array|null $amazon_order_ids
     * @return OrderCreator
     */
    public function setAmazonOrderIds(?array $amazon_order_ids): OrderCreator
    {
        $this->amazon_order_ids = $amazon_order_ids;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getActualFulfillmentSupplySourceId(): ?array
    {
        return $this->actual_fulfillment_supply_source_id;
    }

    /**
     * @param array|null $actual_fulfillment_supply_source_id
     * @return OrderCreator
     */
    public function setActualFulfillmentSupplySourceId(?array $actual_fulfillment_supply_source_id): OrderCreator
    {
        $this->actual_fulfillment_supply_source_id = $actual_fulfillment_supply_source_id;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsIspu(): ?bool
    {
        return $this->is_ispu;
    }

    /**
     * @param bool|null $is_ispu
     * @return OrderCreator
     */
    public function setIsIspu(?bool $is_ispu): OrderCreator
    {
        $this->is_ispu = $is_ispu;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getStoreChainStoreId(): ?string
    {
        return $this->store_chain_store_id;
    }

    /**
     * @param string|null $store_chain_store_id
     * @return OrderCreator
     */
    public function setStoreChainStoreId(?string $store_chain_store_id): OrderCreator
    {
        $this->store_chain_store_id = $store_chain_store_id;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getItemApprovalTypes(): ?array
    {
        return $this->item_approval_types;
    }

    /**
     * @param array|null $item_approval_types
     * @return OrderCreator
     */
    public function setItemApprovalTypes(?array $item_approval_types): OrderCreator
    {
        $this->item_approval_types = $item_approval_types;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getItemApprovalStatus(): ?array
    {
        return $this->item_approval_status;
    }

    /**
     * @param array|null $item_approval_status
     * @return OrderCreator
     */
    public function setItemApprovalStatus(?array $item_approval_status): OrderCreator
    {
        $this->item_approval_status = $item_approval_status;
        return $this;
    }


}