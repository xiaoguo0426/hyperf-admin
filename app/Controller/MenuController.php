<?php

namespace App\Controller;

use App\Exception\InvalidAccessException;
use App\Exception\InvalidArgumentsException;
use App\Exception\InvalidRequestMethodException;
use App\Exception\ResultException;
use App\Logic\MenuLogic;
use App\Util\Data;
use App\Validate\MenuValidate;
use Hyperf\HttpServer\Annotation\AutoController;

/**
 * @menu 菜单管理
 * @AutoController()
 * Class MenuController
 * @package App\Controller
 */
class MenuController extends AbstractController
{
    /**
     * @auth 列表
     */
    public function list(): \Psr\Http\Message\ResponseInterface
    {

        if (! $this->isGet()) {
            throw new InvalidAccessException();
        }

        $logic = di(MenuLogic::class);

        $list = $logic->list();

        $data = Data::toTree($list, 'id', 'pid');

        return $this->response->success($data);

    }

    /**
     * @auth 添加
     */
    public function add()
    {
        if (! $this->isPost()) {
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
        if (! $validate->scene($method)->check($data)) {
            throw new InvalidArgumentsException($validate->getError());
        }

        $logic = di(MenuLogic::class);

        $res = $logic->add($pid, $title, $uri, $params, $icon, $sort);

        if (false === $res) {
            throw new ResultException('添加失败！');
        }

        return $this->response->success([], '添加成功！');

    }

    /**
     * @auth 编辑
     */
    public function edit(): ?\Psr\Http\Message\ResponseInterface
    {
        if (! $this->isPost()) {
            throw new InvalidRequestMethodException('invalid access', 200);
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
        if (! $validate->scene($method)->check($data)) {
            throw new InvalidArgumentsException($validate->getError(), 200);
        }

        $logic = di(MenuLogic::class);

        $res = $logic->edit($id, $pid, $title, $uri, $params, $icon, $sort);

        if (false === $res) {
            throw new ResultException('编辑失败！', 200);
        }

        return $this->response->success([], '编辑成功！');
    }

    /**
     * @auth 删除
     */
    public function del(): ?\Psr\Http\Message\ResponseInterface
    {
        if (! $this->isPost()) {
            throw new InvalidRequestMethodException('invalid access', 200);
        }

        $id = $this->request->post('id', '');

        $data = [
            'id' => $id,
        ];

        $method = __FUNCTION__;
        $validate = new MenuValidate();
        if (! $validate->scene('base')->check($data)) {
            throw new InvalidArgumentsException($validate->getError(), 200);
        }

        $logic = di(MenuLogic::class);

        $res = $logic->$method($id);

        if (false === $res) {
            throw new ResultException('删除失败！', 200);
        }

        return $this->response->success($res, '删除成功！');

    }

    /**
     * @auth 禁用
     */
    public function forbid(): ?\Psr\Http\Message\ResponseInterface
    {
        if (! $this->isPost()) {
            throw new InvalidRequestMethodException('invalid access', 200);
        }

        $id = $this->request->post('id', '');

        $data = [
            'id' => $id,
        ];

        $method = __FUNCTION__;
        $validate = new MenuValidate();
        if (! $validate->scene('base')->check($data)) {
            throw new InvalidArgumentsException($validate->getError(), 200);
        }

        $logic = di(MenuLogic::class);

        $res = $logic->$method($id);

        if (false === $res) {
            throw new ResultException('禁用失败！', 200);
        }

        return $this->response->success($res, '禁用成功！');

    }

    /**
     * @auth 启用
     */
    public function resume()
    {
        if (! $this->isPost()) {
            throw new InvalidRequestMethodException('invalid access', 200);
        }

        $id = $this->request->post('id', '');

        $data = [
            'id' => $id,
        ];

        $method = __FUNCTION__;
        $validate = new MenuValidate();
        if (! $validate->scene('base')->check($data)) {
            throw new InvalidArgumentsException($validate->getError(), 200);
        }

        $logic = di(MenuLogic::class);

        $res = $logic->$method($id);

        if (false === $res) {
            throw new ResultException('启用失败！', 200);
        }

        return $this->response->success($res, '启用成功！');
    }
}