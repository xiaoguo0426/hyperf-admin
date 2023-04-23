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
     * @param $data
     * @return bool
     */
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
