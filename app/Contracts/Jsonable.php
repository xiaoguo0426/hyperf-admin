<?php

declare(strict_types=1);

namespace App\Contracts;

interface Jsonable
{

    public function __toString(): string;
    public function toJson(): void;
}
