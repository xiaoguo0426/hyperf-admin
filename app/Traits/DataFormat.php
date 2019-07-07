<?php


namespace App\Traits;

use Psr\Http\Message\ServerRequestInterface;

trait DataFormat
{
    protected $code = 0;

    protected $msg = '';

    protected $data = [];

    /*public function __construct(ServerRequestInterface $request)
    {
        var_dump($request);
    }

    public function isPost()
    {
    }

    public function isGet()
    {
    }*/

    public function setCode($code)
    {
        $this->code = $code;
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
            'code' => 0,//成功
            'msg' => $this->msg ?: '获取成功！',
            'data' => $data
        ];

    }

    public function error(string $msg, int $code = 1)
    {
        return [
            'code' => $code ?: $this->code,
            'msg' => $msg ?: $this->msg,
            'data' => []
        ];
    }

}