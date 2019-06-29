<?php
declare(strict_types=1);

namespace App\Controller;

use Hyperf\HttpServer\Annotation\AutoController;

/**
 * @AutoController()
 * Class LoginController
 * @package App\Controller
 */
class LoginController extends BaseController
{

    public function index()
    {
        try {
            $method = $this->isPost();
            if (!$this->isPost()) {
                throw new \Exception('invalid access');
            }

            $user_name = $this->request->post('user_name', '');
            $pass_word = $this->request->post('password', '');



        } catch (\Exception $exception) {
            return [
                'success' => false,
                'msg' => $exception->getMessage(),
                'data' => []
            ];
        }

    }

}