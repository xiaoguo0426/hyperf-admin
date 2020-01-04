<?php


namespace App\Controller\Admin;

use App\Exception\InvalidAccessException;
use App\Exception\InvalidArgumentsException;
use App\Exception\InvalidRequestMethodException;
use App\Logic\Admin\UserLogic;
use App\Validate\UserValidate;
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

    public function list()
    {
        if (!$this->isGet()) {
            throw new InvalidRequestMethodException();
        }
        $query = $this->request->all();

        $users = $this->logic->getList($query);

        return $this->response->success($users['list'], $users['count']);
    }

    public function info()
    {
        $user_id = Token::instance()->getUserId();

        $user = $this->logic->getUser($user_id);

        //TODO 去掉password
        return $this->response->success($user);
    }

    /**
     * 保存用户
     */
    public function edit()
    {

        $user_id = Token::instance()->getUserId();

        $role_id = $this->request->post('role_id', '');
        $nickname = $this->request->post('nickname', '');
        $gender = $this->request->post('gender', '');
        $avatar = $this->request->post('avatar', '');
        $mobile = $this->request->post('mobile', '');
        $email = $this->request->post('email', '');
        $remark = $this->request->post('remark', '');

        $data = [
            'id' => $user_id,
            'role_id' => $role_id,
            'nickname' => $nickname,
            'gender' => $gender,
            'avatar' => $avatar,
            'mobile' => $mobile,
            'email' => $email,
            'remark' => $remark,
        ];

        $validate = di(UserValidate::class);

        if (!$validate->scene('edit')->check($data)) {
            throw new InvalidArgumentsException($validate->getError());
        }

        $this->logic->save($user_id, $role_id, $nickname, $gender, $avatar, $mobile, $email, $remark);

        return $this->response->success([], '保存成功！');

    }

    /**
     * 添加会员
     * 【只有admin才有权利添加后台用户】
     */
    public function add()
    {
        //判断是否为admin
        $admin = Token::instance()->getUser();
        if ($admin['username'] !== 'admin') {
            throw new InvalidAccessException('只允许超级管理员添加用户！');
        }

        $username = $this->request->post('username', '');
        $password = $this->request->post('password', '');
        $role_id = $this->request->post('role_id', '');
        $nickname = $this->request->post('nickname', '');
        $gender = $this->request->post('gender', '');
        $avatar = $this->request->post('avatar', '');
        $mobile = $this->request->post('mobile', '');
        $email = $this->request->post('email', '');
        $remark = $this->request->post('remark', '');

        $data = [
            'username' => $username,
            'password' => $password,
            'role_id' => $role_id,
            'nickname' => $nickname,
            'gender' => $gender,
            'avatar' => $avatar,
            'mobile' => $mobile,
            'email' => $email,
            'remark' => $remark,
        ];

        $validate = di(UserValidate::class);

        if (!$validate->scene('add')->check($data)) {
            throw new \Exception($validate->getError());
        }

        $add = $this->logic->add($username, $password, $role_id, $nickname, $gender, $avatar, $mobile, $email, $remark);
        return $this->response->success([], '添加成功！');

    }

}