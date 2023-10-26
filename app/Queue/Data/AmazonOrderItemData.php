<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Queue\Data;

class AmazonOrderItemData extends QueueData implements \JsonSerializable
{
    private int $merchant_id;
    private int $merchant_store_id;
    /**
     * @var string
     */
    private string $order_id;

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
     * @return array
     */
    public function getOrderId(): array
    {
        return explode(',', $this->order_id);
    }

    /**
     * @param array $order_ids
     */
    public function setOrderId(array $order_ids): void
    {
        $this->order_id = implode(',', $order_ids);
    }

    public function toJson(): string
    {
        return json_encode([
            'merchant_id' => $this->merchant_id,
            'merchant_store_id' => $this->merchant_store_id,
            'order_id' => $this->order_id,
        ], JSON_THROW_ON_ERROR);
    }

    public function parse(array $arr): self
    {
        $this->merchant_id = $arr['merchant_id'];
        $this->merchant_store_id = $arr['merchant_store_id'];
        $this->order_id = $arr['order_id'];

        return $this;
    }

    /**
     * @param mixed $json
     * @throws \JsonException
     * @return AmazonOrderItemData
     */
    public static function fromJson(mixed $json): AmazonOrderItemData
    {
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR); // 解码为关联数组
        return new self(
            $data['merchant_id'],
            $data['merchant_store_id'],
            $data['order_id']
        );
    }

    public function jsonSerialize(): mixed
    {
        return [
            'merchant_id' => $this->merchant_id,
            'merchant_store_id' => $this->merchant_store_id,
            'order_id' => $this->order_id,
        ];
    }
}
