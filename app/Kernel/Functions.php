<?php


use Hyperf\Context\ApplicationContext;

if (! function_exists('di')) {
    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param mixed|null $id
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
    /**
     * @throws JsonException
     */
    function is_json($data, $assoc = true): bool
    {
        json_decode($data, $assoc, 512, JSON_THROW_ON_ERROR);
        return json_last_error() === JSON_ERROR_NONE;
    }
}
