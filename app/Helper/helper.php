<?php

function uuid($length)
{
    if (function_exists('random_bytes')) {
        $uuid = bin2hex(random_bytes($length));
    } else if (function_exists('openssl_random_pseudo_bytes')) {
        $uuid = bin2hex(openssl_random_pseudo_bytes($length));
    } else {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $uuid = substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
    }
    return $uuid;
}

/**
 *
 * @param $arr_str
 * @param string $delimiter
 * @return array
 */
function multi_str2tree($arr_str, $delimiter = '/')
{

    $res = array();

    $format = function ($str, $delimiter) {
        $arr = explode($delimiter, $str);
        $result = null;
        // 弹出最后一个元素
        for ($i = count($arr) - 1; $i >= 0; $i--) {
            if ($result === null) {
                $result = $arr[$i];
            } else {
                $result = array($arr[$i] => $result);
            }
        }
        return $result;
    };

    foreach ($arr_str as $string) {
        $res = array_merge_recursive($res, $format($string, $delimiter));
    }

    return $res;
}

/**
 *
 * @param $list
 * @param string $id
 * @param string $pid
 * @param string $son
 * @return array|mixed
 */
function arr2tree($list, $id = 'id', $pid = 'pid', $son = 'sub')
{
    list($tree, $map) = [[], []];
    foreach ($list as $item) $map[$item[$id]] = $item;
    foreach ($list as $item) if (isset($item[$pid]) && isset($map[$item[$pid]])) {
        $map[$item[$pid]][$son][] = &$map[$item[$id]];
    } else {
        $tree[] = &$map[$item[$id]];
    }
    unset($map);
    return $tree;
}