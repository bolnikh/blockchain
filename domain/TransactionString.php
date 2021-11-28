<?php

declare(strict_types = 1);

namespace Domain;

class TransactionString extends TransactionExists
{

    public function __construct(
        private string $string
    )
    {
    }

    public function fromString() : TransactionExists
    {
        $arr = explode(':', $this->string);

        $data = [
            'from' => array_shift($arr),
            'to' => array_shift($arr),
            'amount' => (int) array_shift($arr),
            'created_at' => (int) array_shift($arr),
            'ttl' => (int) array_shift($arr),
            'sign' => array_shift($arr),
            'hash' => array_shift($arr),
        ];

        return new TransactionExists($data);
    }
}