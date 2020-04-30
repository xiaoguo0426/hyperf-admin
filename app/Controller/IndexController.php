<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace App\Controller;

use App\Logic\SettingLogic;
use App\Util\Redis;
use App\Util\RedisLock;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\SuperGlobals\Proxy\Get;
use Psr\Http\Message\ServerRequestInterface;

/**
 * 默认控制器
 * @AutoController()
 * Class IndexController
 * @package App\Controller
 */
class IndexController extends Controller
{
    /**
     * @param ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     * @ignore 默认
     */
    public function index(ServerRequestInterface $request): \Psr\Http\Message\ResponseInterface
    {

        $key = '2020';
        $a = 'bbb';
        try {
            $cid = \Swoole\Coroutine::getCid();
            echo 'Cid:' . $cid . PHP_EOL;

//            RedisLock::lock($key);
            $redis = Redis::getInstance();
            $res = $redis->incr($a);
//            RedisLock::release($key);
            echo 'Cid:' . $cid . ' done';

        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }

        $di = di(SettingLogic::class);

        $setting = $di->getWeb();

        return $this->response->success($setting);

    }

    public function testLock()
    {
        $key = '2020';
        $a = 'bbb';
        try {
            $cid = \Swoole\Coroutine::getCid();
            echo 'Cid:' . $cid . PHP_EOL;

            RedisLock::lock($key);
            $redis = Redis::getInstance();
            $res = $redis->incr($a);
            var_dump($res);
            RedisLock::release($key);
            echo 'Cid:' . $cid . ' done';

        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }


    }

}
