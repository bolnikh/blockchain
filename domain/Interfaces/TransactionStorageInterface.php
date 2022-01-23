<?php


declare(strict_types=1);

namespace Domain\Interfaces;

use Domain\TransactionExists;
use Domain\TransactionNew;

interface TransactionStorageInterface
{
    public function store(TransactionNew|TransactionExists $trx) : void;

    public function getKeyList() : array;

    public function isExists(string $key) : bool;

    public function get(string $key) : TransactionExists|TransactionNew;

    public function delete(string $key) : void;

    public function deleteAllByTtl() : void;

    public function deleteAll(string $iAmSure) : void;
}