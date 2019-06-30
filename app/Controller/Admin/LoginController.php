<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use Hyperf\HttpServer\Annotation\AutoController;
use App\Validate\LoginValidate;
use App\Controller\BaseController;

/**
 * @AutoController()
 * Class LoginController
 * @package App\Controller
 */
class LoginController extends BaseController
{

    public function index()
    {
        var_dump(111);
        try {
            if (!$this->isPost()) {
                throw new \Exception('invalid access');
            }

            $username = $this->request->post('username', '');
            $password = $this->request->post('password', '');

            $data = [
                'username' => $username,
                'password' => $password,
            ];

            $validate = new LoginValidate();

            if (!$validate->check($data)) {
                throw new \Exception($validate->getError());
            }


        } catch (\Exception $exception) {
            return [
                'success' => false,
                'msg' => $exception->getMessage(),
                'data' => []
            ];
        }

    }

}