<?php


declare(strict_types=1);

namespace Domain\Interfaces;

use Domain\TrxExists;
use Domain\TrxNew;

interface TrxStorageInterface
{
    public function store(TrxNew|TrxExists $trx) : void;

    public function getKeyList() : array;

    public function isExists(string $key) : bool;

    public function get(string $key) : TrxExists|TrxNew;

    public function delete(string $key) : void;

    public function deleteAllByTtl() : void;

    public function deleteAll(string $iAmSure) : void;
}