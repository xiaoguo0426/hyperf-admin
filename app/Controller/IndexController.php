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

/**
 * 默认控制器
 * @AutoController()
 * Class IndexController
 * @package App\Controller
 */
class IndexController extends Controller
{
    /**
     * @ignore 默认
     * @return array
     */
    public function index()
    {

        $di = di(SettingLogic::class);

        $setting = $di->getWeb();

        return $this->response->success($setting);

    }
}
