<?php
namespace App\Controller;

use App\Exception\InvalidArgumentsException;
use App\Validate\MenuValidate;
use Hyperf\HttpServer\Annotation\AutoController;

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
    public function getList()
    {

    }

    /**
     * 添加菜单
     */
    public function add()
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
            if (!$validate->scene($method)->check($data)) {
                throw new InvalidArgumentsException($validate->getError(), 200);
            }

            //TODO 该角色下是否存在用户

//            $service = new AuthService();
//
//            $res = $service->$method($id);
//
//            if (false === $res) {
//                throw new \Exception('删除权限失败！', 200);
//            }

            return $this->response->success([], '添加成功！');
        } catch (InvalidArgumentsException $exception) {
            return $this->response->fail($exception->getCode(), $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->response->fail($exception->getCode(), $exception->getMessage());
        }
    }

    /**
     * 编辑菜单
     */
    public function edit()
    {
    }

    /**
     * 删除菜单
     */
    public function del()
    {

    }

}