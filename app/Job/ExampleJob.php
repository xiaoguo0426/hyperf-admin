<?php


namespace App\Job;

use App\Job\JobData\OrderJobData;
use Hyperf\AsyncQueue\Job;

class ExampleJob extends Job
{

    public $params;

    /**
     * 任务执行失败后的重试次数，即最大执行次数为 $maxAttempts+1 次
     *
     * @var int
     */
    protected $maxAttempts = 2;

    public function __construct($params)
    {
        // 这里最好是普通数据，不要使用携带 IO 的对象，比如 PDO 对象
        $this->params = $params;
    }

    /**
     * @inheritDoc
     */
    public function handle()
    {
        // 根据参数处理具体逻辑
        // 通过具体参数获取模型等
        // 这里的逻辑会在 ConsumerProcess 进程中执行
        $orderJobData = $this->params;

        $products = $orderJobData->get('products');
        $coupon_id = $orderJobData->get('coupon_id');
        $address_id = $orderJobData->get('address_id');
        $create_date = $orderJobData->get('create_date');
        var_dump($products);
        var_dump($coupon_id);
        var_dump($address_id);
        var_dump($create_date);
    }
}