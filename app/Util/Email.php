<?php


namespace App\Util;


use App\Exception\BusinessException;
use PHPMailer\PHPMailer\PHPMailer;

class Email
{
    private $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer; //PHPMailer对象
        $this->mail->CharSet = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
        $this->mail->IsSMTP(); // 设定使用SMTP服务
        $this->mail->SMTPDebug = 0; // 关闭SMTP调试功能
        $this->mail->SMTPAuth = true; // 启用 SMTP 验证功能
        $this->mail->SMTPSecure = config('email_smtp_secure'); // 使用安全协议
        $this->mail->Host = config('email_host'); // SMTP 服务器
        $this->mail->Port = config('email_port'); // SMTP服务器的端口号
        $this->mail->Username = config('email_username'); // SMTP服务器用户名
        $this->mail->Password = config('email_password'); // SMTP服务器密码
        $this->mail->SetFrom(config('email_from'), config('email_nickname')); // 邮箱，昵称

    }

    /**
     * 设置收件人
     * @param $address
     * @return $this
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function setAddress($address)
    {
        $this->mail->AddAddress($address);
        return $this;
    }

    /**
     * @param $code
     * @return $this
     */
    public function setCodeTpl($code)
    {
        //TODO 这里以后需要优化下
        $this->mail->Subject = 'Email Verify';
        $this->mail->Body = 'Dear user, this is an email from hacd. Your verification code is:' . $code;
        return $this;
    }

    /**
     * 设置欢迎模板 【just for test】
     * just for test
     * @return $this
     */
    public function setWelcomeTpl()
    {
        $this->mail->Subject = 'Email Verify';
        $this->mail->Body = 'Dear user, this is an email from hacd.';
        return $this;
    }

    /**
     * 设置修改fund-pass模板
     * @param $code
     * @return Email
     */
    public function setFundPassTpl($code)
    {
        $this->mail->Subject = 'Email Verify';
        $this->mail->Body = 'Dear user, this is an email from hacd. Your verification code is:' . $code;
        return $this;
    }

    /**
     * 设置修改fund-pass模板
     * @param $code
     * @return Email
     */
    public function setWithdrawPassTpl($code)
    {
        $this->mail->Subject = 'Email Verify';
        $this->mail->Body = 'Dear user, this is an email from hacd. Your verification code is:' . $code;
        return $this;
    }

    //TODO
    public function setTpl($type)
    {
    }

    public function send()
    {
        $this->_check();

        return $this->mail->Send();
    }

    private function _check()
    {
        if (!$this->mail->Subject) {
            throw new BusinessException('email subject require');
        }

        if (!$this->mail->Body) {
            throw new BusinessException('email body require');
        }
    }

    public function getError()
    {
        return $this->mail->ErrorInfo;
    }

}