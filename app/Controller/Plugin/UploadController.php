<?php

namespace App\Controller\Plugin;

use App\Constants\Constants;
use App\Controller\Controller;

use App\Util\OSS\Signature;
use App\Util\Redis;
use Hyperf\HttpServer\Annotation\AutoController;


/**
 * @menu 资源管理
 * @AutoController()
 * Class UploadController
 * @package App\Controller
 */
class UploadController extends Controller
{
    /**
     * @ignore oss信息
     */
    public function getOss()
    {
        $di = di(Signature::class);

        $dir = Constants::OSS_UPLOAD_TEST;

        return $this->response->success($di->sign($dir));
    }
}