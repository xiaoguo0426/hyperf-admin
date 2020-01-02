<?php


namespace App\Controller;

use App\Exception\InvalidAccessException;
use App\Exception\ResultException;
use App\Logic\AuthLogic;
use App\Service\AuthService;
use App\Validate\AuthValidate;
use Hyperf\Di\Annotation\Inject;
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
     * @Inject()
     * @var AuthLogic
     */
    private $logic;

    /**
     * 列表
     */
    public function list()
    {
        if (!$this->isGet()) {
            throw new InvalidAccessException();
        }

        $list = $this->logic->list();

        return $this->response->success($list);


    }

    /**
     * 添加角色
     */
    public function add()
    {

        if (!$this->isPost()) {
            throw new InvalidAccessException();
        }

        $title = $this->request->post('title', '');
        $desc = $this->request->post('desc', '');

        $data = [
            'title' => $title,
            'desc' => $desc,
        ];

        $validate = new AuthValidate();
        if (!$validate->scene('add')->check($data)) {
            throw new InvalidArgumentsException($validate->getError());
        }

        $logic = new AuthLogic();

        $res = $logic->add($title, $desc);

        if (false === $res) {
            throw new ResultException('新增失败！');
        }

        return $this->response->success([], '新增成功！');

    }

    /**
     * 角色详情
     */
    public function info()
    {
        if (!$this->isGet()) {
            throw new InvalidAccessException();
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

        $logic = new AuthLogic();

        $res = $logic->$method($id);

        return $this->response->success($res);

    }

    /**
     * 删除角色
     */
    public function del()
    {
        if (!$this->isPost()) {
            throw new InvalidAccessException();
        }

        $id = $this->request->post('id', '');

        $data = [
            'id' => $id,
        ];

        $method = __FUNCTION__;
        $validate = new AuthValidate();
        if (!$validate->scene('base')->check($data)) {
            throw new InvalidArgumentsException($validate->getError());
        }

        //TODO 该角色下是否存在用户

        $logic = new AuthLogic();

        $res = $logic->$method($id);

        if (false === $res) {
            throw new ResultException('删除失败！');
        }

        return $this->response->success($res, '删除成功！');

    }

    /**
     * 编辑角色
     */
    public function edit()
    {
        if (!$this->isPost()) {
            throw new InvalidAccessException();
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
            throw new InvalidArgumentsException($validate->getError());
        }

        $logic = new AuthLogic();

        $res = $logic->$method($id, $title, $desc);

        if (false === $res) {
            throw new ResultException('编辑失败！');
        }

        return $this->response->success([], '编辑成功！');

    }

    /**
     * 禁用角色
     */
    public function forbid()
    {
        if (!$this->isPost()) {
            throw new InvalidAccessException();
        }

        $id = $this->request->post('id', '');

        $data = [
            'id' => $id,
        ];
        $method = __FUNCTION__;
        $validate = new AuthValidate();
        if (!$validate->scene('base')->check($data)) {
            throw new InvalidArgumentsException($validate->getError());
        }

        //TODO 该角色下是否存在用户

        $logic = new AuthLogic();

        $res = $logic->$method($id);

        if (false === $res) {
            throw new ResultException('禁用失败！');
        }

        return $this->response->success([], '禁用成功！');

    }

    /**
     * 激活角色
     */
    public function resume()
    {
        if (!$this->isPost()) {
            throw new InvalidAccessException();
        }

        $id = $this->request->post('id', '');

        $data = [
            'id' => $id,
        ];

        $method = __FUNCTION__;
        $validate = new AuthValidate();
        if (!$validate->scene('base')->check($data)) {
            throw new InvalidArgumentsException($validate->getError());
        }

        $logic = new AuthLogic();

        $res = $logic->$method($id);

        if (false === $res) {
            throw new ResultException('激活失败！');
        }

        return $this->response->success([], '激活成功！');

    }

    /**
     * 获取节点数据
     */
    public function getAuthNodes()
    {
        if (!$this->isGet()) {
            throw new InvalidAccessException();
        }

        $id = $this->request->query('id', '');

        $data = [
            'id' => $id,
        ];

        $method = __FUNCTION__;
        $validate = new AuthValidate();
        if (!$validate->scene('base')->check($data)) {
            throw new InvalidArgumentsException($validate->getError());
        }

        $logic = new AuthLogic();

        $list = $logic->$method($id);

        return $this->response->success($list);


    }

    /**
     * 保存节点数据
     */
    public function saveAuthNodes()
    {

        if (!$this->isPost()) {
            throw new InvalidAccessException();
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
            throw new InvalidArgumentsException($validate->getError());
        }

        $service = new AuthService();

        $res = $service->$method($id, $nodes);

        return $this->response->success([], '保存成功！');

    }


}