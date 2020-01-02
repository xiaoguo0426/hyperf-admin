<?php

namespace App\Controller;

use App\Exception\InvalidAccessException;
use App\Exception\InvalidArgumentsException;
use App\Exception\ResultException;
use App\Logic\MenuLogic;
use App\Service\MenuService;
use App\Util\Data;
use App\Validate\MenuValidate;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\Utils\Context;
use Psr\Http\Message\ServerRequestInterface;

/**
 * 菜单管理器
 * @AutoController()
 * Class MenuController
 * @package App\Controller
 */
class MenuController extends Controller
{
    /**
     *列表
     */
    public function list()
    {

        if (!$this->isGet()) {
            throw new InvalidAccessException();
        }

//            $service = new MenuService();
        $logic = new MenuLogic();

        $list = $logic->list();

        $data = Data::toTree($list, 'id', 'pid');

        return $this->response->success($data);

    }

    /**
     * 添加菜单
     */
    public function add()
    {
        if (!$this->isPost()) {
            throw new InvalidAccessException();
        }

        $pid = $this->request->post('pid', '');
        $title = $this->request->post('title', '');
        $uri = $this->request->post('uri', '');
        $params = $this->request->post('params', '');
        $icon = $this->request->post('icon', '');
        $sort = $this->request->post('sort', 0);

        $data = [
            'pid' => $pid,
            'title' => $title,
            'uri' => $uri,
            'params' => $params,
        ];

        $method = __FUNCTION__;
        $validate = new MenuValidate();
        if (!$validate->scene($method)->check($data)) {
            throw new InvalidArgumentsException($validate->getError());
        }

        $logic = new MenuLogic();

        $res = $logic->add($pid, $title, $uri, $params, $icon, $sort);

        if (false === $res) {
            throw new ResultException('添加失败！');
        }

        return $this->response->success([], '添加成功！');

    }

    /**
     * 编辑菜单
     */
    public function edit()
    {
        try {
            if (!$this->isPost()) {
                throw new \Exception('invalid access', 200);
            }

            $id = $this->request->post('id', '');
            $pid = $this->request->post('pid', '');
            $title = $this->request->post('title', '');
            $uri = $this->request->post('uri', '');
            $params = $this->request->post('params', '');
            $icon = $this->request->post('icon', '');
            $sort = $this->request->post('sort', 0);

            $data = [
                'id' => $id,
                'pid' => $pid,
                'title' => $title,
                'uri' => $uri,
                'params' => $params,
            ];

            $method = __FUNCTION__;
            $validate = new MenuValidate();
            if (!$validate->scene($method)->check($data)) {
                throw new InvalidArgumentsException($validate->getError(), 200);
            }

            $logic = new MenuLogic();

            $res = $logic->edit($id, $pid, $title, $uri, $params, $icon, $sort);

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
     * 删除菜单
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
            $validate = new MenuValidate();
            if (!$validate->scene('base')->check($data)) {
                throw new InvalidArgumentsException($validate->getError(), 200);
            }

            $logic = new MenuLogic();

            $res = $logic->$method($id);

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
     * 禁用菜单
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
            $validate = new MenuValidate();
            if (!$validate->scene('base')->check($data)) {
                throw new InvalidArgumentsException($validate->getError(), 200);
            }

            $logic = new MenuLogic();

            $res = $logic->$method($id);

            if (false === $res) {
                throw new \Exception('禁用失败！', 200);
            }

            return $this->response->success($res, '禁用成功！');
        } catch (InvalidArgumentsException $exception) {
            return $this->response->fail($exception->getCode(), $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->response->fail($exception->getCode(), $exception->getMessage());
        }
    }

    /**
     * 启用菜单
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
            $validate = new MenuValidate();
            if (!$validate->scene('base')->check($data)) {
                throw new InvalidArgumentsException($validate->getError(), 200);
            }

            $logic = new MenuLogic();

            $res = $logic->$method($id);

            if (false === $res) {
                throw new \Exception('启用失败！', 200);
            }

            return $this->response->success($res, '启用成功！');
        } catch (InvalidArgumentsException $exception) {
            return $this->response->fail($exception->getCode(), $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->response->fail($exception->getCode(), $exception->getMessage());
        }
    }
}