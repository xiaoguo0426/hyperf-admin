<?php
declare(strict_types=1);

namespace App\Controller\Product;

use App\Controller\Controller;
use App\Exception\InvalidRequestMethodException;
use App\Logic\Product\CategoryLogic;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;

/**
 * @menu 商品分类
 * @AutoController()
 * Class CategoryController
 * @package App\Controller\Product
 */
class CategoryController extends Controller
{
    /**
     * @Inject()
     * @var CategoryLogic
     */
    private $logic;

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function list()
    {
        if (!$this->isGet()) {
            throw new InvalidRequestMethodException();
        }
        $query = $this->request->all();

        $users = $this->logic->list($query);

        return $this->response->success($users['list'], $users['count']);
    }
}