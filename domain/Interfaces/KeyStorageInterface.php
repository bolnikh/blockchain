<?php

declare(strict_types=1);

namespace Domain\Interfaces;


interface KeyStorageInterface
{
    public function get(string $key) : string;

    public function isExists(string $key) : bool;

    public function delete(string $key) : void;

    public function set(string $key, string $value) : void;

    /**
     * Список всех ключей
     *
     * @return array keys
     */
    public function list() : array;

    /**
     * Удаляет все данные
     */
    public function deleteAll(string $iAmSure) : void;
}