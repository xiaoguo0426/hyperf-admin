<?php
declare(strict_types=1);

namespace App\Controller\Product;

use App\Controller\AbstractController;
use App\Exception\EmptyException;
use App\Exception\InvalidAccessException;
use App\Exception\InvalidArgumentsException;
use App\Exception\InvalidRequestMethodException;
use App\Exception\ResultException;
use App\Logic\Product\CategoryLogic;
use App\Validate\CategoryValidate;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;

/**
 * @menu 商品分类
 * @AutoController()
 * Class CategoryController
 * @package App\Controller\Product
 */
class CategoryController extends AbstractController
{
    /**
     * @Inject()
     * @var CategoryLogic
     */
    private $logic;

    /**
     * @auth 列表
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function list(): \Psr\Http\Message\ResponseInterface
    {
        if (!$this->isGet()) {
            throw new InvalidRequestMethodException();
        }
        $query = $this->request->query();

        $tree = $this->logic->getListCache();

        if (!$tree){

            $category = $this->logic->listWithNoPage($query);

            $tree = $this->logic->toTree($category['list']);

            $this->logic->setListCache($tree);
        }

        return $this->response->success($tree);
    }

    /**
     * @auth 详情
     */
    public function info(): \Psr\Http\Message\ResponseInterface
    {
        if (!$this->isGet()) {
            throw new InvalidAccessException();
        }

        $id = $this->request->query('id', 0);

        $res = [];

        if ($id) {
            $res = $this->logic->info((int)$id);

            if (!$res) {
                throw new EmptyException('分类不存在！');
            }

            $res = $res->toArray();

        } else {
            $res['title'] = '';
            $res['desc'] = '';
            $res['sort'] = '';
            $res['id'] = '';
        }

        $query = [
            'parent_id' => 0
        ];
        $category = $this->logic->listWithNoPage($query);

        $res['categories'] = $this->logic->toTree($category['list']);

        return $this->response->success($res);
    }

    /**
     * @auth 编辑
     */
    public function edit(): \Psr\Http\Message\ResponseInterface
    {
        if (!$this->isPost()) {
            throw new InvalidAccessException();
        }

        $id = $this->request->post('id', '');
        $parent_id = (int)$this->request->post('parent_id', '');
        $title = $this->request->post('title', '');
        $sort = (int)$this->request->post('sort', 0);
        $desc = $this->request->post('desc', '');

        $data = [
            'id' => $id,
            'parent_id' => $parent_id,
            'title' => $title,
            'sort' => $sort,
            'desc' => $desc,
        ];

        $method = __FUNCTION__;
        $validate = di(CategoryValidate::class);
        if (!$validate->scene($method)->check($data)) {
            throw new InvalidArgumentsException($validate->getError());
        }

        $res = $this->logic->$method((int)$id, $parent_id, $title, $sort, $desc);

        if (false === $res) {
            throw new ResultException('编辑失败！');
        }

        $this->logic->clearListCache();

        return $this->response->success([], 0, '编辑成功！');

    }

    /**
     * @auth 新增
     */
    public function add(): \Psr\Http\Message\ResponseInterface
    {
        if (!$this->isPost()) {
            throw new InvalidAccessException();
        }

        $parent_id = (int)$this->request->post('parent_id', '');
        $title = $this->request->post('title', '');
        $sort = (int)$this->request->post('sort', 0);
        $desc = $this->request->post('desc', '');

        $data = [
            'parent_id' => $parent_id,
            'title' => $title,
            'sort' => $sort,
            'desc' => $desc,
        ];

        $method = __FUNCTION__;
        $validate = di(CategoryValidate::class);
        if (!$validate->scene($method)->check($data)) {
            throw new InvalidArgumentsException($validate->getError());
        }

        $res = $this->logic->$method($parent_id, $title, $sort, $desc);

        if (false === $res) {
            throw new ResultException('新增失败！');
        }

        $this->logic->clearListCache();

        return $this->response->success([], 0, '新增成功！');

    }

    /**
     * @auth 删除
     */
    public function del(): \Psr\Http\Message\ResponseInterface
    {
        if (!$this->isPost()) {
            throw new InvalidAccessException();
        }

        $id = $this->request->post('id', '');

        $data = [
            'id' => $id,
        ];

        $method = __FUNCTION__;
        $validate = di(CategoryValidate::class);
        if (!$validate->scene('base')->check($data)) {
            throw new InvalidArgumentsException($validate->getError());
        }

        $res = $this->logic->$method((int)$id);

        if (false === $res) {
            throw new ResultException('删除失败！');
        }

        //清除redis数据
        $this->logic->clearListCache();

        return $this->response->success([
            'id' => $id
        ], 0, '删除成功！');

    }


    /**
     * @auth 禁用
     */
    public function forbid(): \Psr\Http\Message\ResponseInterface
    {
        if (!$this->isPost()) {
            throw new InvalidAccessException();
        }

        $id = $this->request->post('id', '');

        $data = [
            'id' => $id,
        ];
        $method = __FUNCTION__;
        $validate = di(CategoryValidate::class);
        if (!$validate->scene('base')->check($data)) {
            throw new InvalidArgumentsException($validate->getError());
        }

        //TODO 该角色下是否存在用户

//        $logic = new AuthLogic();

        $res = $this->logic->$method((int)$id);

        if (false === $res) {
            throw new ResultException('禁用失败！');
        }

        $this->logic->clearListCache();

        return $this->response->success([], 0, '禁用成功！');

    }

    /**
     * @auth 启用
     */
    public function resume(): \Psr\Http\Message\ResponseInterface
    {
        if (!$this->isPost()) {
            throw new InvalidAccessException();
        }

        $id = $this->request->post('id', '');

        $data = [
            'id' => $id,
        ];

        $method = __FUNCTION__;
        $validate = di(CategoryValidate::class);
        if (!$validate->scene('base')->check($data)) {
            throw new InvalidArgumentsException($validate->getError());
        }

        $res = $this->logic->$method((int)$id);

        if (false === $res) {
            throw new ResultException('启用失败！');
        }

        $this->logic->clearListCache();

        return $this->response->success([], 0, '启用成功！');

    }
}