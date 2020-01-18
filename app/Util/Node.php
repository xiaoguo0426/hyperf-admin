<?php
declare(strict_types=1);

namespace App\Util;

use App\Exception\FileNotFoundException;

class Node
{
    /**
     * 忽略控制器
     * @var array
     */
    private static $ignoreController = [
        'Controller', 'IndexController'
    ];

    /**
     * 忽略方法名
     * @var array
     */
    private static $ignoreAction = [
        '__construct', 'isPost', 'isGet', 'isAjax'
    ];

    /**
     * @param $dir
     * @return array
     * @throws \ReflectionException
     */
    public static function getClassNodes($dir): array
    {
        if (!is_dir($dir)) {
            throw new FileNotFoundException('目录不存在！');
        }
        $nodes = [];
        self::eachController($dir, static function (\ReflectionClass $reflection, $prenode) use (&$nodes) {
            [$node, $comment] = [str_replace('Controller', '', trim($prenode, '/')), $reflection->getDocComment()];
            $menu = preg_replace('/^\/\*\*\*(.*?)\*.*?$/', '$1', preg_replace("/\s/", '', $comment));
            if (stripos($menu, '@menu') !== false) {

                $nodes[$node] = str_replace('@menu', '', $menu);
            }
        });
        return $nodes;
    }

    /**
     *
     * @param $dir
     * @param $callable
     * @throws \ReflectionException
     */
    public static function eachController($dir, $callable): void
    {
        $app_namespace = config('app_namespace');
        foreach (self::scanDir($dir) as $file) {
            if (!preg_match("|/Controller/(.+)\.php$|", strtr($file, '\\', '/'), $matches)) continue;
            $controller = $matches[1];
            foreach (self::$ignoreController as $ignore) {
                if (stripos($controller, $ignore) === 0) {
                    continue 2;
                }
            }
            $class = substr(strtr($app_namespace . $matches[0], '/', '\\'), 0, -4);
            if (class_exists($class)) {
                call_user_func($callable, new \ReflectionClass($class), $controller);
            }
        }
    }

    /**
     * 获取方法节点列表
     * @param $dir
     * @return array
     * @throws \ReflectionException
     */
    public static function getAuthMethodNodes($dir): array
    {
        $nodes = [];
        self::eachController($dir, static function (\ReflectionClass $reflection, $prenode) use (&$nodes) {
            $parentClassMethods = $reflection->getParentClass()->getMethods();
            foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                $action = $method->getName();
                foreach ($parentClassMethods as $parentClassMethod) {
                    if ($parentClassMethod->name === $action) {
                        continue 2;
                    }
                }
                foreach (self::$ignoreAction as $ignore) {
                    if (stripos($action, $ignore) === 0) {
                        continue 2;
                    }
                }
                $node = str_replace('Controller', '', $prenode) . '/' . $action;
                $auth = preg_replace('/^\/\*\*\*(.*?)\*.*?$/', '$1', preg_replace("/\s/", '', $method->getDocComment()));
                $flag = '@auth';
                if (stripos($auth, $flag) === false) {
                    continue;
                }
                $nodes[$node] = str_replace($flag, '', $auth);

            }
        });
        return $nodes;
    }

    public static function getIgnoreMethodNodes($dir): array
    {
        $nodes = [];
        self::eachController($dir, static function (\ReflectionClass $reflection, $prenode) use (&$nodes) {
            $parentClassMethods = $reflection->getParentClass()->getMethods();
            foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                $action = $method->getName();
                foreach ($parentClassMethods as $parentClassMethod) {
                    if ($parentClassMethod->name === $action) {
                        continue 2;
                    }
                }
                foreach (self::$ignoreAction as $ignore) {
                    if (stripos($action, $ignore) === 0) {
                        continue 2;
                    }
                }
                $node = str_replace('Controller', '', $prenode) . '/' . $action;
                $auth = preg_replace('/^\/\*\*\*(.*?)\*.*?$/', '$1', preg_replace("/\s/", '', $method->getDocComment()));
                $flag = '@ignore';
                if (stripos($auth, $flag) === false) {
                    continue;
                }
                $nodes[] = strtolower($node);
            }
        });
        return $nodes;
    }


    /**
     * @param $dir
     * @param array $data
     * @param string $ext
     * @return array
     */
    public static function scanDir($dir, $data = [], $ext = 'php'): array
    {
        foreach (scandir($dir) as $curr) {
            if (strpos($curr, '.') !== 0) {
                $path = realpath($dir . DIRECTORY_SEPARATOR . $curr);
                if (is_dir($path)) {
                    $data = array_merge($data, self::scanDir($path));
                } elseif (pathinfo($path, PATHINFO_EXTENSION) === $ext) {
                    $data[] = $path;
                }
            }
        }
        return $data;
    }

}