<?php

declare(strict_types = 1);

namespace Domain;

class Hash
{
    public static function getHash(string $string) : string
    {
        return hash('sha256', $string);
    }

}