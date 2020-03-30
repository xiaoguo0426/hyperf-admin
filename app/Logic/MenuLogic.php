<?php
declare(strict_types=1);

namespace App\Logic;


use App\Exception\EmptyException;
use App\Service\MenuService;

class MenuLogic
{

    public function list(): array
    {

        return di(MenuService::class)->list();
    }

    public function add(int $pid, string $title, string $uri = '', string $params = '#', string $icon = '', $sort = 0): bool
    {
        return di(MenuService::class)->add($pid, $title, $uri, $params, $icon, $sort);
    }

    public function info(int $id): array
    {
        return di(MenuService::class)->info($id);
    }

    public function edit(int $id, int $pid, string $title, string $uri = '', string $params = '#', string $icon = '', $sort = 0): array
    {
        $di = di(MenuService::class);

        $info = $di->info($id);

        if (empty($info)) {
            throw new EmptyException();
        }

        return $di->edit($id, $pid, $title, $uri, $params, $icon, $sort);

    }

    public function del(int $id)
    {

        $di = di(MenuService::class);

        $info = $di->info($id);

        if (empty($info)) {
            throw new EmptyException();
        }

        return $di->del($id);

    }

    public function forbid(int $id)
    {
        $di = di(MenuService::class);

        $info = $di->info($id);

        if (!$info) {
            throw new EmptyException();
        }

        return $di->forbid($id);
    }

    public function resume(int $id)
    {
        $di = di(MenuService::class);

        $info = $di->info($id);

        if (!$info) {
            throw new EmptyException();
        }

        return $di->resume($id);

    }

}