<?php


namespace App\Controller;

use App\Service\AuthService;
use App\Validate\AuthValidate;
use App\Exception\InvalidArgumentsException;
use Hyperf\HttpServer\Annotation\AutoController;

/**
 * 权限管理
 * @AutoController()
 * Class AuthController
 * @package App\Controller
 */
class AuthController extends Controller
{

    public function list()
    {
        try {
            if (!$this->isGet()) {
                throw new \Exception('invalid access', 200);
            }


            $service = new AuthService();

            $list = $service->list();

            return $this->response->success($list);

        } catch (\Exception $exception) {
            return $this->response->fail($exception->getCode(), $exception->getMessage());
        }
    }

    /**
     * 添加角色
     */
    public function add()
    {
        try {
            if (!$this->isPost()) {
                throw new \Exception('invalid access', 200);
            }

            $title = $this->request->post('title', '');
            $desc = $this->request->post('desc', '');

            $data = [
                'title' => $title,
                'desc' => $desc,
            ];

            $validate = new AuthValidate();
            if (!$validate->scene('add')->check($data)) {
                throw new InvalidArgumentsException($validate->getError(), 200);
            }

            $service = new AuthService();

            $res = $service->add($title, $desc);

            if (false === $res) {
                throw new \Exception('新增权限失败！', 200);
            }

            return $this->response->success([], '新增权限成功！');

        } catch (InvalidArgumentsException $exception) {
            return $this->response->fail($exception->getCode(), $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->response->fail($exception->getCode(), $exception->getMessage());
        }
    }

    /**
     * 删除角色
     */
    public function del()
    {
        try {

            if (!$this->isPost()) {
                throw new \Exception('invalid access', 200);
            }

            $id = $this->request->post('id', '');

            $data = [
                'id' => $id,
            ];

            $validate = new AuthValidate();
            if (!$validate->scene('del')->check($data)) {
                throw new InvalidArgumentsException($validate->getError(), 200);
            }

            $service = new AuthService();

            $res = $service->del($id);

            if (false === $res) {
                throw new \Exception('新增权限失败！', 200);
            }

            return $this->response->success([], '新增权限成功！');

        } catch (\Exception $exception) {

        }
    }

    /**
     * 编辑角色
     */
    public function edit()
    {
        try {

        } catch (\Exception $exception) {

        }
    }

    /**
     * 禁用角色
     */
    public function forbid()
    {
        try {

        } catch (\Exception $exception) {

        }
    }

    /**
     * 激活角色
     */
    public function resume()
    {
        try {

        } catch (\Exception $exception) {

        }
    }

}