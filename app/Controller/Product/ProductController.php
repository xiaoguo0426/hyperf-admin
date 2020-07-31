<?php
declare(strict_types=1);

namespace App\Controller\Product;

use App\Controller\AbstractController;
use App\Exception\EmptyException;
use App\Exception\InvalidAccessException;
use App\Exception\InvalidRequestMethodException;
use App\Logic\Product\ProductLogic;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;

/**
 * @menu 商品管理
 * @AutoController()
 * Class ProductController
 * @package App\Controller\Product
 */
class ProductController extends AbstractController
{

    /**
     * @Inject()
     * @var ProductLogic
     */
    private $logic;

    /**
     * @auth 列表
     */
    public function list(): \Psr\Http\Message\ResponseInterface
    {
        if (!$this->isGet()) {
            throw new InvalidRequestMethodException();
        }
        $query = $this->request->query();

        $res = $this->logic->list($query);

        return $this->response->success($res['list'], $res['count']);

    }

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
                throw new EmptyException('商品不存在！');
            }

            $res = $res->toArray();

        } else {
            $res['cate_id'] = '';
            $res['title'] = '';
            $res['logo'] = '';
            $res['images'] = '';
            $res['desc'] = '';
            $res['contents'] = '';
            $res['sort'] = '';
            $res['status'] = '';
        }

        return $this->response->success($res);
    }

    /**
     * @auth 新增
     */
    public function add()
    {
    }

    /**
     * @auth 编辑
     */
    public function edit()
    {

    }

    /**
     * @auth 禁用
     */
    public function forbid()
    {

    }

    /**
     * @auth 启用
     */
    public function resume()
    {

    }

}