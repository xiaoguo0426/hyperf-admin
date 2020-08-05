<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Controller;

use App\Logic\SettingLogic;
use App\Util\RedisLock2;
use Hyperf\HttpServer\Annotation\AutoController;
use Swoole\Coroutine;

/**
 * @AutoController()
 * Class IndexController
 * @package App\Controller
 */
class IndexController extends AbstractController
{
    public function index(): \Psr\Http\Message\ResponseInterface
    {
//redis锁
        //注意key的规则
//        $lock = make(RedisLock2::class);
//
//        try {
//            $key = 'test';
//            $lock->run(function () use ($key) {
//                echo date('Y-m-d H:i:s') . '--' . $key . 'do somethings ' . \Hyperf\Utils\Coroutine::id() . PHP_EOL;
//                Coroutine::sleep(5);
//
//            }, $key);
//        } catch (\Exception $e) {
//        }


        $di = di(SettingLogic::class);

        $setting = $di->getWeb();

        return $this->response->success($setting);
    }
}
