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
/**
 * 默认控制器
 * Class IndexController
 * @package App\Controller
 */
class IndexController extends Controller
{
    public function index()
    {
        $method = $this->request->getMethod();

        return [
            'method' => $method,
            'message' => "这是一个默认的请求",
        ];
    }
}
