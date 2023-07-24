<?php

namespace App\Queue\Data;

class AmazonFinanceListFinancialEventsByGroupIdData extends QueueData
{

    private int $merchant_id;
    private int $merchant_store_id;

    private string $financial_event_group_id;

    /**
     * @return int
     */
    public function getMerchantId(): int
    {
        return $this->merchant_id;
    }

    /**
     * @param int $merchant_id
     */
    public function setMerchantId(int $merchant_id): void
    {
        $this->merchant_id = $merchant_id;
    }

    /**
     * @return int
     */
    public function getMerchantStoreId(): int
    {
        return $this->merchant_store_id;
    }

    /**
     * @param int $merchant_store_id
     */
    public function setMerchantStoreId(int $merchant_store_id): void
    {
        $this->merchant_store_id = $merchant_store_id;
    }

    /**
     * @return string
     */
    public function getFinancialEventGroupId(): string
    {
        return $this->financial_event_group_id;
    }

    /**
     * @param string $financial_event_group_id
     */
    public function setFinancialEventGroupId(string $financial_event_group_id): void
    {
        $this->financial_event_group_id = $financial_event_group_id;
    }


    public function toJson(): string
    {
        return json_encode([
            'merchant_id' => $this->merchant_id,
            'merchant_store_id' => $this->merchant_store_id,
            'financial_event_group_id' => $this->financial_event_group_id,
        ], JSON_THROW_ON_ERROR);
    }

    public function parse(array $arr): AmazonFinanceListFinancialEventsByGroupIdData
    {
        $this->setMerchantId($arr['merchant_id']);
        $this->setMerchantStoreId($arr['merchant_store_id']);
        $this->setFinancialEventGroupId($arr['financial_event_group_id']);
        return $this;
    }
}