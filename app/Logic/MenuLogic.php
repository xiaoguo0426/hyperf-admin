<?php


namespace App\Logic;


use App\Exception\EmptyException;
use App\Service\MenuService;

class MenuLogic
{

    public function list(): array
    {

        $service = new MenuService();

        $list = $service->list();

        return $list;
    }

    public function add(int $pid, string $title, string $uri = '', string $params = '#', string $icon = '', $sort = 0): bool
    {

        $service = new MenuService();

        return $service->add($pid, $title, $uri, $params, $icon, $sort);
    }

    public function info(int $id): array
    {
        $service = new MenuService();

        return $service->info($id);
    }

    public function edit(int $id, int $pid, string $title, string $uri = '', string $params = '#', string $icon = '', $sort = 0): array
    {
        $service = new MenuService();

        $info = $service->info($id);

        if (empty($info)) {
            throw new EmptyException();
        }

        return $service->edit($id, $pid, $title, $uri, $params, $icon, $sort);

    }

    public function del(int $id)
    {

        $service = new MenuService();

        $info = $service->info($id);

        if (empty($info)) {
            throw new EmptyException();
        }

        return $service->del($id);

    }

    public function forbid(int $id)
    {
        $service = new MenuService();

        $info = $service->info($id);

        if (!$info) {
            throw new EmptyException();
        }

        return $service->forbid($id);
    }

    public function resume(int $id)
    {
        $service = new MenuService();

        $info = $service->info($id);

        if (!$info) {
            throw new EmptyException();
        }

        return $service->resume($id);

    }

}