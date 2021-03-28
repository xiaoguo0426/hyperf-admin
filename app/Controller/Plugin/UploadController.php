<?php

namespace App\Controller\Plugin;

use App\Constants\Constants;
use App\Controller\AbstractController;
use App\Util\OSS\Signature;
use Hyperf\HttpServer\Annotation\AutoController;


/**
 * @menu 资源管理
 * @AutoController()
 * Class UploadController
 * @package App\Controller
 */
class UploadController extends AbstractController
{
    /**
     * @ignore oss信息
     */
    public function getOss(): \Psr\Http\Message\ResponseInterface
    {
        $di = di(Signature::class);

        $dir = Constants::OSS_UPLOAD_TEST;

        return $this->response->success($di->sign($dir));
    }
}