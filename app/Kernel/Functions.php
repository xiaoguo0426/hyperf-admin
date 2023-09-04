<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */
use Hyperf\Context\ApplicationContext;

if (! function_exists('di')) {
    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param null|mixed $id
     *
     * @return mixed|\Psr\Container\ContainerInterface
     */
    function di($id = null)
    {
        $container = ApplicationContext::getContainer();
        if ($id) {
            return $container->get($id);
        }

        return $container;
    }
}

if (! function_exists('is_json')) {
    function is_json($data): bool
    {
        try {
            $result = json_decode($data, false, 512, JSON_THROW_ON_ERROR);
            return json_last_error() === JSON_ERROR_NONE && is_object($result);
        } catch (JsonException $jsonException) {
            return false;
        }
    }
}
