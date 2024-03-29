<?php

declare(strict_types=1);

function uuid($length)
{
    if (function_exists('random_bytes')) {
        $uuid = bin2hex(random_bytes($length));
    } elseif (function_exists('openssl_random_pseudo_bytes')) {
        $uuid = bin2hex(openssl_random_pseudo_bytes($length));
    } else {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $uuid = substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
    }
    return $uuid;
}

/**
 * @param $list
 *
 * @return array|mixed
 */
function arr2tree($list, string $id = 'id', string $pid = 'pid', string $son = 'sub')
{
    [$tree, $map] = [[], []];
    foreach ($list as $item) {
        $map[$item[$id]] = $item;
    }
    foreach ($list as &$item) {
        if (isset($item[$pid]) && isset($map[$item[$pid]])) {
            $map[$item[$pid]][$son][] = &$map[$item[$id]];
        } else {
            $tree[] = &$map[$item[$id]];
        }
        unset($item);
    }
    unset($map);
    return $tree;
}

/**
 * 一维数据数组生成数据树
 *
 * @param array $list 数据列表
 * @param string $id ID Key
 * @param string $pid 父ID Key
 *
 * @return array
 */
function arr2table(array $list, string $id = 'id', string $pid = 'pid', string $path = 'path', string $ppath = ''): array
{
    $tree = [];
    foreach (arr2tree($list, $id, $pid) as $attr) {
        $attr[$path] = "{$ppath}-{$attr[$id]}";
        $attr['sub'] = $attr['sub'] ?? [];
        $attr['spt'] = substr_count($ppath, '-');
        $attr['spl'] = str_repeat('　├　', $attr['spt']);
        $sub = $attr['sub'];
        unset($attr['sub']);
        $tree[] = $attr;
        if (! empty($sub)) {
            $tree = array_merge($tree, arr2table($sub, $id, $pid, $path, $attr[$path]));
        }
    }
    return $tree;
}

function is_json($data, $assoc = true)
{
    json_decode($data, $assoc);
    return json_last_error() === JSON_ERROR_NONE;
}
