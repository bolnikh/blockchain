<?php

declare(strict_types=1);

namespace Domain\Interfaces;


interface KeyBranchStorageInterface
{
    public function get(string $key, int $branch) : string;

    public function isExists(string $key, int $branch) : bool;

    public function delete(string $key, int $branch) : void;

    public function set(string $key, int $branch, string $value) : void;

    /**
     * Список всех ключей, branch
     *
     * @return array keys
     */
    public function list() : array;

    /**
     * Список всех ключей определенной ветки
     *
     * @param int $branch
     * @return array keys
     */
    public function listBranch(int $branch) : array;

    /**
     * Удаляет все данные
     * @param string $iAmSure
     */
    public function deleteAll(string $iAmSure) : void;

    /**
     * Удалить все узлы
     * @param int
     */
    public function deleteBranch(int $branch) : void;

    /**
     * Скопировать данные из одной ветки в другую
     * @param int $branchFrom
     * @param int $branchTo
     */
    public function copyBranchTo(int $branchFrom, int $branchTo) : void;
}