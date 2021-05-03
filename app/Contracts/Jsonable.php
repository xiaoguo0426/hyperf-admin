<?php


namespace App\Contracts;


interface Jsonable
{
    public function toJson();

    public function __toString(): string;
}