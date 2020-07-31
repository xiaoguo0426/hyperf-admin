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
use Hyperf\HttpServer\Annotation\AutoController;

/**
 * @AutoController()
 * Class IndexController
 * @package App\Controller
 */
class IndexController extends AbstractController
{
    public function index(): \Psr\Http\Message\ResponseInterface
    {
        $di = di(SettingLogic::class);

        $setting = $di->getWeb();

        return $this->response->success($setting);
    }
}
