<?php

declare(strict_types=1);

namespace App\Actions;

use App\Classes\Config;
use App\Classes\ServiceLocator;
use Domain\BlockChainBalanceValidate;
use Domain\BlockExists;
use Domain\BlockNew;
use Domain\Interfaces\BlockChainStorageInterface;
use Domain\Interfaces\TrxStorageInterface;

class CreateNewBlockAction
{

    private ServiceLocator $sl;
    private Config $config;
    private TrxStorageInterface $ts;
    private BlockChainStorageInterface $bs;
    private BlockChainStorageInterface $bsn;

    public function __construct()
    {
        $this->sl = ServiceLocator::instance();

        /**
         * @var Config
         */
        $this->config = $this->sl->get('Config');

        /**
         * @var TrxStorageInterface
         */
        $this->ts = $this->sl->get('TrxStorage');

        /**
         * @var BlockChainStorageInterface
         */
        $this->bs = $this->sl->get('BlockChainStorage');

        /**
         * @var BlockChainStorageInterface
         */
        $this->bsn = $this->sl->get('BlockChainNewStorage');
    }

    public function run()
    {
        $maxBlockId = $this->bs->getMaxId();
        $new_id = $maxBlockId + 1;
        $prev_block_hash = BlockExists::EmptyPrevBlockHash;
        if ($maxBlockId > 0) {
            $maxBlock = $this->bs->getById($maxBlockId);
            $prev_block_hash = $maxBlock->hash;
        }

        $trxs = $this->getTrxs();

        $nb = new BlockNew([
            'id' => $new_id,
            'prev_block_hash' => $prev_block_hash,
            'trx' => $trxs,
            'difficulty' => $this->config->difficulty,
            'is_mining' => $this->config->is_mining,
            'mining_private_key' => $this->config->node_private_key,
            'mining_award' => $this->config->mining_award,
        ]);

        $nb->findProof();

        $this->bs->store($nb);
        $this->bsn->store($nb);
    }

    /**
     * Подбираем подходящие для нового блока транзакции
     * @return array
     */
    protected function getTrxs(): array
    {
        $bbv = new BlockChainBalanceValidate();
        $trxs = [];
        foreach ($this->ts->getKeyList() as $key) {
            $trx = $this->ts->get($key);
            if (!$trx->isValidForNewBlock()) {
                continue;
            }
            if ($this->bs->isTrxUsed($trx->hash)) {
                continue;
            }
            if (!$bbv->validateNewTrxBalanceAgainstTrxs($this->bs, $trxs, $trx)) {
                continue;
            }
            $trxs[] = $trx;
        }
        return $trxs;
    }
}