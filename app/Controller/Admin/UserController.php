<?php


namespace App\Controller\Admin;

use App\Logic\Admin\UserLogic;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use App\Controller\Controller;
use App\Util\Token;

/**
 * @AutoController()
 * Class UserController
 * @package App\Controller\Admin
 */
class UserController extends Controller
{
    /**
     * @Inject()
     * @var UserLogic
     */
    private $logic;

    public function getUser()
    {
        try {
            $user_id = Token::instance()->getUserId();

            $user = $this->logic->getUser($user_id);

            if (empty($user)) {
                throw new \Exception('用户不存在！');
            }

            //TODO 去掉password
            return $this->response->success($user->toArray());
        } catch (\Exception $exception) {
            return $this->response->error(1, '登录成功！');
        }
    }

}