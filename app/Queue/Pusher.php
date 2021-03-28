<?php


namespace App\Queue;


use App\Job\ExampleJob;
use App\Job\JobData\OrderItemJobData;
use App\Job\JobData\OrderJobData;
use Hyperf\AsyncQueue\Driver\DriverFactory;
use Hyperf\AsyncQueue\Driver\DriverInterface;

class Pusher
{
    /**
     * @var DriverInterface
     */
    protected $driver;

    public function __construct(DriverFactory $driverFactory)
    {
        $this->driver = $driverFactory->get('default');
    }

    /**
     * 生产消息.
     * @param $params array 数据
     * @param int $delay 延时时间 单位秒
     * @return bool
     */
    public function push($params, int $delay = 20): bool
    {
        // 这里的 `ExampleJob` 会被序列化存到 Redis 中，所以内部变量最好只传入普通数据
        // 同理，如果内部使用了注解 @Value 会把对应对象一起序列化，导致消息体变大。
        // 所以这里也不推荐使用 `make` 方法来创建 `Job` 对象。

        $products = [
            [
                'product_id' => 1,
                'sku_id' => 1,
                'num' => 1
            ],
            [
                'product_id' => 2,
                'sku_id' => 2,
                'num' => 2
            ],
            [
                'product_id' => 3,
                'sku_id' => 3,
                'num' => 3
            ],
        ];

        $orderProducts = [];
        foreach ($products as $product) {
            $itemJobData = new OrderItemJobData($product['product_id'], $product['sku_id'], $product['num']);
            $orderProducts[] = $itemJobData;
        }

        $orderJobData = new OrderJobData($orderProducts,11,22);

        return $this->driver->push(new ExampleJob($orderJobData), $delay);
    }

}