<?php

declare(strict_types = 1);

namespace Domain;

class Difficulty
{

    public function __construct(
        private string $difficulty
    ) {}

    public function check(string $string) : bool
    {
        return $this->difficulty >= $string;
    }
}