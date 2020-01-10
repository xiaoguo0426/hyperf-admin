<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\EmptyException;
use App\Exception\InvalidAccessException;
use App\Exception\ResultException;
use App\Logic\AuthLogic;
use App\Service\AuthService;
use App\Util\Auth;
use App\Util\Prefix;
use App\Util\Redis;
use App\Validate\AuthValidate;
use Hyperf\Di\Annotation\Inject;
use App\Exception\InvalidArgumentsException;
use Hyperf\HttpServer\Annotation\AutoController;

/**
 * @menu 权限管理
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
     * @auth 列表
     */
    public function list()
    {
        if (!$this->isGet()) {
            throw new InvalidAccessException();
        }

        $query = $this->request->all();

        $data = $this->logic->list($query);

        return $this->response->success($data['list'], $data['count']);


    }

    /**
     * @auth 添加
     */
    public function add()
    {

        if (!$this->isPost()) {
            throw new InvalidAccessException();
        }

        $title = $this->request->post('title', '');
        $desc = $this->request->post('desc', '');
        $nodes = $this->request->post('nodes', []);

        $data = [
            'title' => $title,
            'desc' => $desc,
        ];

        $validate = new AuthValidate();
        if (!$validate->scene('add')->check($data)) {
            throw new InvalidArgumentsException($validate->getError());
        }

        $res = $this->logic->add($title, $nodes, $desc);

        if (false === $res) {
            throw new ResultException('新增失败！');
        }

        return $this->response->success([], 0, '新增成功！');

    }

    /**
     * @auth 详情
     */
    public function info()
    {
        if (!$this->isGet()) {
            throw new InvalidAccessException();
        }

        $id = $this->request->query('id', 0);

        $res = [];

        if ($id) {
            $res = $this->logic->info((int)$id);

            if (!$res) {
                throw new EmptyException('角色不存在！');
            }

            $res = $res->toArray();

        } else {
            $res['title'] = '';
            $res['desc'] = '';
            $res['id'] = '';
        }

        $all_nodes = Auth::getAllNodes();

        $auths = Auth::getNodes((int)$id);

        foreach ($all_nodes as &$first) {
            if (isset($first['sub'])) {

                $sub_first = $first['sub'];

                foreach ($sub_first as &$second) {
                    if (isset($second['sub'])) {
                        $sub_second = $second['sub'];

                        foreach ($sub_second as &$third) {
                            //暂支持3层控制器
                            $hash = Auth::hash($third['node']);

                            $third['checked'] = in_array($hash, $auths, true);

                        }
                        unset($third);
                        $second['sub'] = $sub_second;
                        $second['checked'] = in_array(Auth::hash($second['node']), $auths, true);;
                    } else {
                        $hash = Auth::hash($second['node']);
                        $second['checked'] = in_array($hash, $auths, true);
                    }
                }
                unset($second);
                $first['sub'] = $sub_first;
            }
            $first['checked'] = in_array(Auth::hash($first['node']), $auths, true);

        }
        unset($first);

        $res['auths'] = $all_nodes;

        return $this->response->success($res);

    }

    /**
     * @auth 删除
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

        $res = $this->logic->$method((int)$id);

        if (false === $res) {
            throw new ResultException('删除失败！');
        }

        return $this->response->success([
            'id' => $id
        ], 0, '删除成功！');

    }

    /**
     * @auth 编辑
     */
    public function edit()
    {
        if (!$this->isPost()) {
            throw new InvalidAccessException();
        }

        $id = $this->request->post('id', '');
        $title = $this->request->post('title', '');
        $desc = $this->request->post('desc', '');
        $nodes = $this->request->post('nodes', []);

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

        $res = $this->logic->$method((int)$id, $title, $nodes, $desc);

        if (false === $res) {
            throw new ResultException('编辑失败！');
        }

        return $this->response->success([], 0, '编辑成功！');

    }

    /**
     * @auth 禁用
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

//        $logic = new AuthLogic();

        $res = $this->logic->$method((int)$id);

        if (false === $res) {
            throw new ResultException('禁用失败！');
        }

        return $this->response->success([], 0, '禁用成功！');

    }

    /**
     * @auth 启用
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

        $res = $this->logic->$method((int)$id);

        if (false === $res) {
            throw new ResultException('启用失败！');
        }

        return $this->response->success([], 0, '启用成功！');

    }
}