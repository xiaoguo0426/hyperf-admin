<?php
declare(strict_types=1);

namespace App\Controller;

use Hyperf\HttpServer\Annotation\AutoController;

/**
 * @AutoController()
 * Class BaseController
 * @package App\Controller
 */
class BaseController extends Controller
{

    protected $msg = '';

    protected $data = [];

    public function isPost()
    {
        return $this->request->isMethod('post');
    }

    public function isGet()
    {
        return $this->request->isMethod('get');
    }

    public function setMsg($msg)
    {
        $this->msg = $msg;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function success(array $data = [])
    {
        return [
            'success' => true,
            'msg' => $this->msg ?: '获取成功！',
            'data' => $data
        ];

    }

    public function error(string $msg)
    {
        return [
            'success' => false,
            'msg' => $msg ?: $this->msg,
            'data' => []
        ];
    }

}