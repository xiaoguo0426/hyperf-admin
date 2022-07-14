<?php

declare(strict_types=1);

/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 *
 * @document https://doc.hyperf.io
 *
 * @contact  group@hyperf.io
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Controller;

use App\Logic\SettingLogic;
use App\Util\RedisHash\StudentRedisHash;
use App\Util\RedisHash\TeacherRedisHash;
use Hyperf\HttpServer\Annotation\AutoController;

/**
 * @AutoController()
 * Class IndexController
 *
 * @package App\Controller
 */
class IndexController extends AbstractController
{
    public function index(): \Psr\Http\Message\ResponseInterface
    {

        //redis锁
        //注意key的规则
        // try {
        //     $product_ids = [1];

        //     mt_srand();
        //     $user_id = mt_rand(1000, 9999);

        //     $key = 'product::' . $product_ids[mt_rand(0, count($product_ids) - 1)];

        //     $default = make(DefaultRedis::class);
        //     $test = make(TestRedis::class);
        //     $pool = [
        //         0 => $default,
        //         1 => $test
        //     ];

        //     $mo = ($user_id % 2);

        //     $redis = $pool[$mo];

        //     $stock = $redis->get($key);
        //     if ($stock <= 0) {
        //         throw new \Exception($key . ' 商品已售罄');
        //     }

        //     echo $mo . PHP_EOL;

        //     $lock = make(RedisLock2::class, [$pool[$mo]]);

        //     $lock->run(function () use ($key, $user_id, $redis) {
        //         //获得锁后才能尝试扣减库存
        //         $curStock = $redis->decr($key);

        //         if ($curStock < 0) {
        //             throw new \Exception($key . '超卖');
        //         }
        //         echo 'user_id:' . $user_id . '获得了抢购资格 pid:' . \Hyperf\Utils\Coroutine::id() . PHP_EOL;

        //         echo date('Y-m-d H:i:s') . '--' . $key . ' 正在处理user_id ' . $user_id . '的业务 cut stock  pid:' . \Hyperf\Utils\Coroutine::id() . PHP_EOL;
        //         echo '用户 ' . $user_id . ' 业务处理完成' . $key . PHP_EOL;

        //     }, $key, 10);
        //     echo '总数+1' . PHP_EOL;
        //     Redis::incr('product::total');

        // } catch (\Throwable $e) {
        //     echo $e->getMessage() . PHP_EOL;
        // }

        $di = di(SettingLogic::class);
        $di->saveWeb('hyper-admin','xiaoguo0426','http://admin.hyperf.test','hyperf,admin','hyperf,admin','@2022');

        $setting = $di->getWeb();

        return $this->response->success($setting);
    }

    public function test(): void
    {
        $student = [
            'id' => '123',
            'name' => 'test',
            'age' => '28',
        ];
        $studentHash = new StudentRedisHash();

        $studentHash->init($student);
        echo $studentHash->id . PHP_EOL;
        echo $studentHash->name . PHP_EOL;
        echo $studentHash->age . PHP_EOL;

        unset($studentHash->id);

        $student = [
            'id' => '123123',
            'name' => 'xiaoguo',
            'age' => '28',
        ];
        $studentHash = new TeacherRedisHash();

        $studentHash->init($student);
        echo $studentHash->id . PHP_EOL;
        echo $studentHash->name . PHP_EOL;
        echo $studentHash->age . PHP_EOL;

        unset($studentHash->id);

//        var_dump($studentHash . '');
    }
}
