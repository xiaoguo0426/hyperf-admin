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
        $method = $this->isPost();
        var_dump($method);

        $user_name = $this->request->post('user_name', '');
        $pass_word = $this->request->post('pass_word', '');


    }

}