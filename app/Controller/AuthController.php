<?php


namespace App\Controller;

use App\Service\AuthService;
use App\Service\NodeService;
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
    /**
     * 列表
     * @return \Psr\Http\Message\ResponseInterface
     */
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
                throw new \Exception('新增失败！', 200);
            }

            return $this->response->success([], '新增成功！');

        } catch (InvalidArgumentsException $exception) {
            return $this->response->fail($exception->getCode(), $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->response->fail($exception->getCode(), $exception->getMessage());
        }
    }

    /**
     * 角色详情
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
            if (!$validate->scene('base')->check($data)) {
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
            if (!$validate->scene('base')->check($data)) {
                throw new InvalidArgumentsException($validate->getError(), 200);
            }

            //TODO 该角色下是否存在用户

            $service = new AuthService();

            $res = $service->$method($id);

            if (false === $res) {
                throw new \Exception('删除失败！', 200);
            }

            return $this->response->success($res, '删除成功！');
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
            if (!$validate->scene('base')->check($data)) {
                throw new InvalidArgumentsException($validate->getError(), 200);
            }

            $service = new AuthService();

            $res = $service->$method($id, $title, $desc);

            if (false === $res) {
                throw new \Exception('编辑失败！', 200);
            }

            return $this->response->success([], '编辑成功！');
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
            if (!$validate->scene('base')->check($data)) {
                throw new InvalidArgumentsException($validate->getError(), 200);
            }

            //TODO 该角色下是否存在用户

            $service = new AuthService();

            $res = $service->$method($id);

            if (false === $res) {
                throw new \Exception('禁用失败！', 200);
            }

            return $this->response->success([], '禁用成功！');
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
            if (!$validate->scene('base')->check($data)) {
                throw new InvalidArgumentsException($validate->getError(), 200);
            }

            $service = new AuthService();

            $res = $service->$method($id);

            if (false === $res) {
                throw new \Exception('激活失败！', 200);
            }

            return $this->response->success([], '激活成功！');
        } catch (InvalidArgumentsException $exception) {
            return $this->response->fail($exception->getCode(), $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->response->fail($exception->getCode(), $exception->getMessage());
        }
    }

    /**
     * 获取节点数据
     */
    public function getAuthNodes()
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
            if (!$validate->scene('base')->check($data)) {
                throw new InvalidArgumentsException($validate->getError(), 200);
            }

            $service = new AuthService();

            $list = $service->$method($id);

            return $this->response->success($list);

        } catch (InvalidArgumentsException $exception) {
            return $this->response->fail($exception->getCode(), $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->response->fail($exception->getCode(), $exception->getMessage());
        }

    }

    /**
     * 保存节点数据
     */
    public function saveAuthNodes()
    {

        try {
            if (!$this->isPost()) {
                throw new \Exception('invalid access', 200);
            }

            $id = $this->request->post('id', '');
            $nodes = $this->request->post('nodes', []);

            $data = [
                'id' => $id,
                'nodes' => $nodes
            ];

            $method = __FUNCTION__;
            $validate = new AuthValidate();
            if (!$validate->scene('saveAuthNodes')->check($data)) {
                throw new InvalidArgumentsException($validate->getError(), 200);
            }

            $service = new AuthService();

            $res = $service->$method($id, $nodes);

            return $this->response->success([], '保存成功！');

        } catch (InvalidArgumentsException $exception) {
            return $this->response->fail($exception->getCode(), $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->response->fail($exception->getCode(), $exception->getMessage());
        }
    }

}