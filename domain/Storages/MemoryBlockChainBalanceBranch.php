<?php


declare(strict_types=1);

namespace Domain\Storages;

use Domain\Interfaces\BlockChainBalanceBranchInterface;
use Domain\Interfaces\BlockChainStorageInterface;

class MemoryBlockChainBalanceBranch implements BlockChainBalanceBranchInterface
{
    private array $balanceCache = [];
    private array $branchMaxKey = [];

    public function __construct(
        private BlockChainStorageInterface $bs
    )
    {
    }

    public function deleteAll(string $iAmSure) : void
    {
        if ($iAmSure != 'I am sure to delete all') {
            return;
        }

        $this->balanceCache = [];
        $this->branchMaxKey = [];
    }

    /**
     * Заполнение баланса ветки
     *
     * Предполагается что сначала инициируется DefBranch
     * поскольку если данных в ветке нет, то мы их берем из DefBranch
     * @param int $branch
     */
    public function initBalance(int $branch = self::DefBranch) : void
    {

    }

    /**
     * Добавляем этот блок в баланс
     *
     * @param int $block
     * @param int $branch
     */
    public function addBlockBalance(int $block_id, int $branch = self::DefBranch) : void
    {
        $block = $this->bs->getById($block_id);

        foreach ($block->trx as $trx) {
            $this->removeFrom($trx->from, $trx->amount, $block_id, $branch);
            $this->addTo($trx->to, $trx->amount, $block_id, $branch);

        }
    }


    private function removeFrom($from, $amount, $block_id, $branch) {

    }

    private function addTo($to, $amount, $block_id, $branch) {

    }

    /**
     * Удалить блок баланс
     *
     * А вдруг уже есть более старшие блоки? Должен быть максимальным блоком
     * @param int $block
     * @param int $branch
     */
    public function deleteBlockBalance(int $block_id, int $branch = self::DefBranch) : void
    {

    }

    /**
     * Удалить все данные баланса ветки
     * @param int $branch
     */
    public function removeBranchBalance(int $branch) : void
    {

    }

    /**
     * Сам баланс
     * @param string $from
     * @param int $block_id
     * @param int $branch
     * @return int
     */
    public function balance(string $from, int $block_id = 0, int $branch = self::DefBranch): int
    {
        // TODO: Implement balance() method.
    }
}