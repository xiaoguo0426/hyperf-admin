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
use Hyperf\HttpServer\Annotation\AutoController;
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
     * @return array
     * @ignore 默认
     */
    public function index(ServerRequestInterface $request)
    {

        $di = di(SettingLogic::class);

        $setting = $di->getWeb();

        var_dump($request->getHeaderLine('Host'));
        var_dump($request->getHeaderLine('X-Real-IP'));
        var_dump($request->getHeaderLine('X-Real-PORT'));
        var_dump($request->getHeaderLine('X-Forwarded-For'));

        return $this->response->success($setting);

    }
}
