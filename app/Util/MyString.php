<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util;

class MyString
{
    /**
     * 找出两个字符串相同的部分.
     */
    public static function findCommonSubstring(string $str1, string $str2): string
    {
        $commonSubstring = '';
        $length = strlen($str1);

        for ($i = 0; $i < $length; ++$i) {
            for ($j = $i + 1; $j <= $length; ++$j) {
                $substring = substr($str1, $i, $j - $i);
                if (str_contains($str2, $substring) && strlen($substring) > strlen($commonSubstring)) {
                    $commonSubstring = $substring;
                }
            }
        }

        return $commonSubstring;
    }

    /**
     * 找出两个字符串前缀相同的部分.
     */
    public static function findCommonPrefix(string $str1, string $str2): string
    {
        $commonPrefix = '';
        $length = min(strlen($str1), strlen($str2));

        for ($i = 0; $i < $length; ++$i) {
            if ($str1[$i] !== $str2[$i]) {
                break;
            }
            $commonPrefix .= $str1[$i];
        }

        return $commonPrefix;
    }

    /**
     * 找出数组前缀相同的部分.
     * @param string[]
     * @param mixed $array
     */
    public static function findCommonPrefixes($array): array
    {
        $result = [];
        $count = count($array);

        for ($i = 0; $i < $count - 1; ++$i) {
            $prefix = '';
            $minLength = min(strlen($array[$i]), strlen($array[$i + 1]));

            for ($j = 0; $j < $minLength; ++$j) {
                if ($array[$i][$j] !== $array[$i + 1][$j]) {
                    break;
                }
                $prefix .= $array[$i][$j];
            }

            if (! empty($prefix)) {
                $result[] = $prefix;
            }
        }

        return $result;
    }
}
