<?php

declare(strict_types=1);

namespace App\Contracts;

interface Arrayable
{
    public function toArray(): array;
}
