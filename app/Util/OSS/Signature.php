<?php

declare(strict_types=1);

namespace App\Util\OSS;

use App\Exception\InvalidConfigException;
use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;

class Signature
{

    // Domain Types
    public const OSS_HOST_TYPE_NORMAL = 'normal';//http://bucket.oss-cn-hangzhou.aliyuncs.com/object
    public const OSS_HOST_TYPE_IP = 'ip';  //http://1.1.1.1/bucket/object
    public const OSS_HOST_TYPE_SPECIAL = 'special'; //http://bucket.guizhou.gov/object
    public const OSS_HOST_TYPE_CNAME = 'cname';  //http://mydomain.com/object
    /**
     * @var ConfigInterface
     */
    protected $config;

    private $accessKeyId;
    private $accessKeySecret;
    private $host;
    private $isCname;
    private $hostname;
    private $schema;

    private $useSSL;
    private $hostType;

    private $callbackUrl;

    private $timeout;//超时时间  单位 s   默认120
    private $maxSize;//文件大小  配置文件填写单位是 kb 默认2028
    private $maxSizeRaw;

    public function __construct(ContainerInterface $container, ConfigInterface $config)
    {
        $this->config = $config->get('aliyun-oss.default', ConfigInterface::class);

        $this->accessKeyId = $this->config['accessKeyId'] ?? '';
        $this->accessKeySecret = $this->config['accessKeySecret'] ?? '';
        $this->host = $this->config['host'] ?? '';

        $this->isCname = (bool) $this->config['isCname'];

        if (empty($this->accessKeyId)) {
            throw new InvalidConfigException('access key id is empty');
        }
        if (empty($this->accessKeySecret)) {
            throw new InvalidConfigException('access key secret is empty');
        }
        if (empty($this->host)) {
            throw new InvalidConfigException('endpoint is empty');
        }
        $this->host = trim($this->host ?? '', '/');

        $this->hostname = $this->checkEndpoint($this->host, $this->isCname);
        $this->timeout = $this->config['timeout'] ?? 120;

        $this->maxSizeRaw = isset($this->config['maxSize']) ? (int) $this->config['maxSize'] : 2048;
        //单位  字节B
        $this->maxSize = $this->maxSizeRaw * 1024;

        $this->callbackUrl = $this->config['callbackUrl'] ?? '';
    }

    public function getOssClient()
    {
        return make(self::class);
    }

    /**
     * 上传目录
     *
     * @return array
     */
    public function sign(string $dir = ''): array
    {
        $callback_param = [
            'callbackUrl' => $this->callbackUrl,
            'callbackBody' => 'filename=${object}&size=${size}&mimeType=${mimeType}&height=${imageInfo.height}&width=${imageInfo.width}',
            'callbackBodyType' => 'application/x-www-form-urlencoded',
        ];

        $callback_string = json_encode($callback_param);

        $base64_callback_body = base64_encode($callback_string);

        $now = time();
        $expire = $this->timeout;  //设置该policy超时时间是10s. 即这个policy过了这个有效时间，将不能访问。
        $end = $now + $expire;

        $expiration = Util::gmtISO8601($end);

        $condition = [0 => 'content-length-range', 1 => 0, 2 => $this->maxSize];
        $conditions[] = $condition;

        // 表示用户上传的数据，必须是以$dir开始，不然上传会失败，这一步不是必须项，只是为了安全起见，防止用户通过policy上传到别人的目录。
        $start = [0 => 'starts-with', 1 => '$key', 2 => $dir];
        $conditions[] = $start;
        $arr = ['expiration' => $expiration, 'conditions' => $conditions];
        $policy = json_encode($arr);
        $base64_policy = base64_encode($policy);
        $string_to_sign = $base64_policy;
        $signature = base64_encode(hash_hmac('sha1', $string_to_sign, $this->accessKeySecret, true));

        return [
            'accessKeyId' => $this->accessKeyId,
            'host' => $this->host,
            'policy' => $base64_policy,
            'signature' => $signature,
            'expire' => $end,
            'callback' => $base64_callback_body,
            'dir' => $dir,// 这个参数是设置用户上传文件时指定的前缀。
            'maxSize' => $this->maxSizeRaw,
        ];
    }

    private function checkEndpoint($endpoint, $isCName)
    {
        $ret_endpoint = null;
        if (strpos($endpoint, 'http://') === 0) {
            $this->schema = 'http';
            $ret_endpoint = substr($endpoint, strlen('http://'));
        } elseif (strpos($endpoint, 'https://') === 0) {
            $this->schema = 'https';
            $ret_endpoint = substr($endpoint, strlen('https://'));
            $this->useSSL = true;
        } else {
            $this->schema = '';
            $ret_endpoint = $endpoint;
        }

        $ret_endpoint = Util::getHostPortFromEndpoint($ret_endpoint);

        if ($isCName) {
            $this->hostType = self::OSS_HOST_TYPE_CNAME;
        } elseif (Util::isIPFormat($ret_endpoint)) {
            $this->hostType = self::OSS_HOST_TYPE_IP;
        } else {
            $this->hostType = self::OSS_HOST_TYPE_NORMAL;
        }
        return $ret_endpoint;
    }
}
