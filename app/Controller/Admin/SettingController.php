<?php


namespace App\Controller\Admin;

use App\Controller\Controller;
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
        
        return $this->response->success([]);
    }

    /**
     * @ignore 保存网站设置
     */
    public function saveWeb()
    {
    }

    /**
     * @auth 邮件服务
     */
    public function getSMTP()
    {
    }

    /**
     * @ignore 保存邮件服务
     */
    public function saveSMTP()
    {
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