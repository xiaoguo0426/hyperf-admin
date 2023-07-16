<?php

namespace App\Queue\Data;

interface QueueDataInterface
{
    public function toArr(string $json): array;

    public function toJson(): string;

    public function parse(array $arr);
}