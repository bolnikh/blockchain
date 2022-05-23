<?php


declare(strict_types=1);

namespace Domain\Storages;

use Domain\Interfaces\KeyBranchStorageInterface;
use Domain\Interfaces\KeyStorageInterface;


class KeyBranchStorage implements KeyBranchStorageInterface
{
    const DEL = '_';

    public function __construct(
        private KeyStorageInterface $ks
    )
    {

    }


    public function getKey(string $key, int $branch) : string
    {
        return $key.self::DEL.$branch;
    }

    public function getKeyParts(string $key) : array
    {
        $a = explode(self::DEL, $key);
        if (sizeof($a) <= 1) {
            throw new \Exception('Bad branch key format');
        }

        $branch = intval(array_pop($a));

        return ['key' => implode(self::DEL, $a), 'branch' => $branch];
    }


    public function get(string $key, int $branch) : string
    {
        return $this->ks->get($this->getKey($key, $branch));
    }


    public function isExists(string $key, int $branch) : bool
    {
        return $this->ks->isExists($this->getKey($key, $branch));
    }

    public function delete(string $key, int $branch) : void
    {
        $this->ks->delete($this->getKey($key, $branch));
    }

    public function set(string $key, int $branch, string $value) : void
    {
        $this->ks->set($this->getKey($key, $branch), $value);
    }

    /**
     * Список всех ключей, branch
     *
     * @return array keys
     */
    public function list() : array
    {
        $res = [];

        foreach ($this->ks->list() as $k) {
            $res[] = $this->getKeyParts($k);
        }

        return $res;
    }

    /**
     * Список всех ключей определенной ветки
     *
     * @param int $branch
     * @return array keys
     */
    public function listBranch(int $branch) : array
    {
        $res = [];

        foreach ($this->list() as $a) {
            if ($a['branch'] == $branch) {
                $res[] = $a;
            }
        }

        return $res;
    }

    /**
     * Удаляет все данные
     * @param string $iAmSure
     */
    public function deleteAll(string $iAmSure) : void
    {
        if ($iAmSure != 'I am sure to delete all') {
            return;
        }

        $this->ks->deleteAll($iAmSure);
    }

    /**
     * Удалить все узлы
     * @param int
     */
    public function deleteBranch(int $branch) : void
    {
        foreach ($this->listBranch($branch) as $a) {
            $this->ks->delete($this->getKey($a['key'], $a['branch']));
        }
    }

    /**
     * Скопировать данные из одной ветки в другую
     * @param int $branchFrom
     * @param int $branchTo
     */
    public function copyBranchTo(int $branchFrom, int $branchTo) : void
    {
        foreach ($this->listBranch($branchFrom) as $a) {
            $this->set($a['key'], $branchTo, $this->get($a['key'], $branchFrom));
        }
    }
}