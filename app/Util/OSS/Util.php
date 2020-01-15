<?php


namespace App\Util\OSS;


use DateTime;

class Util
{

    /**
     * Check if the endpoint is in the IPv4 format, such as xxx.xxx.xxx.xxx:port or xxx.xxx.xxx.xxx.
     *
     * @param string $endpoint The endpoint to check.
     * @return boolean
     */
    public static function isIPFormat($endpoint): bool
    {
        $ip_array = explode(":", $endpoint);
        $hostname = $ip_array[0];
        $ret = filter_var($hostname, FILTER_VALIDATE_IP);
        if (!$ret) {
            return false;
        }

        return true;
    }


    /**
     * Get the host:port from endpoint.
     * @param $endpoint
     * @return false|string
     */
    public static function getHostPortFromEndpoint($endpoint)
    {
        $str = $endpoint;
        $pos = strpos($str, "://");
        if ($pos !== false) {
            $str = substr($str, $pos + 3);
        }

        $pos = strpos($str, '#');
        if ($pos !== false) {
            $str = substr($str, 0, $pos);
        }

        $pos = strpos($str, '?');
        if ($pos !== false) {
            $str = substr($str, 0, $pos);
        }

        $pos = strpos($str, '/');
        if ($pos !== false) {
            $str = substr($str, 0, $pos);
        }

        $pos = strpos($str, '@');
        if ($pos !== false) {
            $str = substr($str, $pos + 1);
        }

        return $str;
    }

    public static function gmtISO8601($time) {
        $dtStr = date('c', $time);
        $mydatetime = new DateTime($dtStr);
        $expiration = $mydatetime->format(DateTime::ISO8601);
        $pos = strpos($expiration, '+');
        $expiration = substr($expiration, 0, $pos);
        return $expiration. 'Z';
    }
}