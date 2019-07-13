<?php


namespace App\Util;


use App\Exception\FileException;

class Node
{
    /**
     * 忽略控制名的前缀
     * @var array
     */
    private static $ignoreController = [
        'api.', 'wap.', 'web.',
    ];

    /**
     * 忽略控制的方法名
     * @var array
     */
    private static $ignoreAction = [
        '_', 'redirect', 'assign', 'callback',
        'initialize', 'success', 'error', 'fetch',
    ];

    /**
     * @param $dir
     * @return mixed
     * @throws \ReflectionException
     */
    public static function getClassTreeNode($dir)
    {
        if (!is_dir($dir)) {
            throw new FileException('目录不存在！');
        }

        self::eachController($dir, function (\ReflectionClass $reflection, $prenode) use (&$nodes) {
            list($node, $comment) = [trim($prenode, '/'), $reflection->getDocComment()];
            $nodes[$node] = preg_replace('/^\/\*\*\*(.*?)\*.*?$/', '$1', preg_replace("/\s/", '', $comment));
            if (stripos($nodes[$node], '@') !== false) $nodes[$node] = '';
        });
        return $nodes;
    }

    /**
     *
     * @param $dir
     * @param $callable
     * @throws \ReflectionException
     */
    public static function eachController($dir, $callable)
    {
        foreach (self::scanDir($dir) as $file) {
            if (!preg_match("|/(\w+)/Controller/(.+)\.php$|", strtr($file, '\\', '/'), $matches)) continue;
            list($module, $controller) = [$matches[1], strtr($matches[2], '/', '.')];
            foreach (self::$ignoreController as $ignore) if (stripos($controller, $ignore) === 0) continue 2;
            if (class_exists($class = substr(strtr(env('app_namespace') . $matches[0], '/', '\\'), 0, -4))) {
                call_user_func($callable, new \ReflectionClass($class), Node::parseString("{$module}/{$controller}/"));
            }
        }
    }

    /**
     * @param $dir
     * @param array $data
     * @param string $ext
     * @return array
     */
    public static function scanDir($dir, $data = [], $ext = 'php')
    {
        foreach (scandir($dir) as $curr) if (strpos($curr, '.') !== 0) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $curr);
            if (is_dir($path)) $data = array_merge($data, self::scanDir($path));
            elseif (pathinfo($path, PATHINFO_EXTENSION) === $ext) $data[] = $path;
        }
        return $data;
    }

    /**
     * @param $node
     * @return string
     */
    public static function parseString($node)
    {
        if (count($nodes = explode('/', $node)) > 1) {
            $dots = [];
            foreach (explode('.', $nodes[1]) as $dot) {
                $dots[] = trim(preg_replace("/[A-Z]/", "_\\0", $dot), "_");
            }
            $nodes[1] = join('.', $dots);
        }
        return strtolower(join('/', $nodes));
    }

}