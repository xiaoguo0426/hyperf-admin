<?php


namespace App\Controller\Admin;

use App\Controller\Controller;
use App\Exception\InvalidAccessException;
use App\Exception\ResultException;
use App\Logic\SettingLogic;
use Hyperf\HttpServer\Annotation\AutoController;

/**
 * @menu 系统管理
 * @AutoController()
 * Class MainController
 * @package App\Controller\Admin
 */
class SettingController extends Controller
{

    /**
     * @auth 网站设置
     */
    public function getWeb()
    {
        $di = di(SettingLogic::class);

        $setting = $di->getWeb();

        return $this->response->success($setting);
    }

    /**
     * @auth 保存网站设置
     */
    public function saveWeb()
    {

        if (!$this->isPost()) {
            throw new InvalidAccessException();
        }

        $site = $this->request->post('site', '');
        $domain = $this->request->post('domain', '');
        $keywords = $this->request->post('keywords', '');
        $desc = $this->request->post('desc', '');
        $copyright = $this->request->post('copyright', '');

        $data = [
            'site' => $site,
            'domain' => $domain,
            'keywords' => $keywords,
            'desc' => $desc,
            'copyright' => $copyright
        ];

        $di = di(SettingLogic::class);

        $setting = $di->saveWeb($data);

        if (!$setting) {
            throw new ResultException('保存失败！');
        }
        return $this->response->success([], 0, '保存成功！');

    }

    /**
     * @auth 邮件服务
     */
    public function getSMTP()
    {
        $di = di(SettingLogic::class);

        $setting = $di->getSMTP();

        return $this->response->success($setting);
    }

    /**
     * @ignore 保存邮件服务
     */
    public function saveSMTP()
    {

        if (!$this->isPost()) {
            throw new InvalidAccessException();
        }

        $server = $this->request->post('server', '');
        $port = $this->request->post('port', '');
        $email = $this->request->post('email', '');
        $nickname = $this->request->post('nickname', '');
        $password = $this->request->post('password', '');

        $data = [
            'server' => $server,
            'port' => $port,
            'email' => $email,
            'nickname' => $nickname,
            'password' => $password
        ];

        $di = di(SettingLogic::class);

        $setting = $di->saveSMTP($data);

        if (!$setting) {
            throw new ResultException('保存失败！');
        }
        return $this->response->success([], 0, '保存成功！');


    }

    /**
     * @auth 微信
     */
    public function getWechat()
    {
    }

    /**
     * @ignore 保存微信
     */
    public function saveWechat()
    {
    }

    /**
     * @auth 支付宝
     */
    public function getAliPay()
    {
    }

    /**
     * @ignore 保存支付宝设置
     */
    public function saveAliPay()
    {
    }
}