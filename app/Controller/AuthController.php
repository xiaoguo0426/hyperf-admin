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
     * 详情
     */
    public function info()
    {
        try {
            if (!$this->isGet()) {
                throw new \Exception('invalid access', 200);
            }

            $id = $this->request->query('id', '');

            $data = [
                'id' => $id,
            ];

            $method = __FUNCTION__;
            $validate = new AuthValidate();
            if (!$validate->scene($method)->check($data)) {
                throw new InvalidArgumentsException($validate->getError(), 200);
            }

            $service = new AuthService();

            $res = $service->$method($id);

            return $this->response->success($res);
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

            $method = __FUNCTION__;
            $validate = new AuthValidate();
            if (!$validate->scene($method)->check($data)) {
                throw new InvalidArgumentsException($validate->getError(), 200);
            }

            $service = new AuthService();

            $res = $service->$method($id);

            if (false === $res) {
                throw new \Exception('删除权限失败！', 200);
            }

            return $this->response->success($res, '删除权限成功！');
        } catch (InvalidArgumentsException $exception) {
            return $this->response->fail($exception->getCode(), $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->response->fail($exception->getCode(), $exception->getMessage());
        }
    }

    /**
     * 编辑角色
     */
    public function edit()
    {
        try {
            if (!$this->isPost()) {
                throw new \Exception('invalid access', 200);
            }

            $id = $this->request->post('id', '');
            $title = $this->request->post('title', '');
            $desc = $this->request->post('desc', '');

            $data = [
                'id' => $id,
                'title' => $title,
                'desc' => $desc,
            ];

            $method = __FUNCTION__;
            $validate = new AuthValidate();
            if (!$validate->scene($method)->check($data)) {
                throw new InvalidArgumentsException($validate->getError(), 200);
            }

            $service = new AuthService();

            $res = $service->$method($id, $title, $desc);

            if (false === $res) {
                throw new \Exception('编辑权限失败！', 200);
            }

            return $this->response->success([], '编辑权限成功！');
        } catch (InvalidArgumentsException $exception) {
            return $this->response->fail($exception->getCode(), $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->response->fail($exception->getCode(), $exception->getMessage());
        }
    }

    /**
     * 禁用角色
     */
    public function forbid()
    {
        try {
            if (!$this->isPost()) {
                throw new \Exception('invalid access', 200);
            }

            $id = $this->request->post('id', '');

            $data = [
                'id' => $id,
            ];
            $method = __FUNCTION__;
            $validate = new AuthValidate();
            if (!$validate->scene($method)->check($data)) {
                throw new InvalidArgumentsException($validate->getError(), 200);
            }

            $service = new AuthService();

            $res = $service->$method($id);

            if (false === $res) {
                throw new \Exception('禁用权限失败！', 200);
            }

            return $this->response->success([], '禁用权限成功！');
        } catch (InvalidArgumentsException $exception) {
            return $this->response->fail($exception->getCode(), $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->response->fail($exception->getCode(), $exception->getMessage());
        }
    }

    /**
     * 激活角色
     */
    public function resume()
    {
        try {
            if (!$this->isPost()) {
                throw new \Exception('invalid access', 200);
            }

            $id = $this->request->post('id', '');

            $data = [
                'id' => $id,
            ];

            $method = __FUNCTION__;
            $validate = new AuthValidate();
            if (!$validate->scene($method)->check($data)) {
                throw new InvalidArgumentsException($validate->getError(), 200);
            }

            $service = new AuthService();

            $res = $service->$method($id);

            if (false === $res) {
                throw new \Exception('激活权限失败！', 200);
            }

            return $this->response->success([], '激活权限成功！');
        } catch (InvalidArgumentsException $exception) {
            return $this->response->fail($exception->getCode(), $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->response->fail($exception->getCode(), $exception->getMessage());
        }
    }

}