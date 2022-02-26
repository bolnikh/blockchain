<?php


declare(strict_types=1);

namespace Domain\Factory;

use Domain\BlockExists;
use Domain\BlockNew;
use Domain\Storages\BlockChainStorageMemory;
use Domain\TrxNew;
use Exception;

class BlockChainFactory
{
    private array $balance = [];
    private array $trx = [];

    public function __construct(
        private BlockChainStorageMemory|null $bcs = null,
        private int $blocksNumberToAdd = 10,
        private array $key_list = [],
        private bool $is_mining = true,
        private int $mining_award = 100,
        private string $difficulty = '00f'
    ){
        if (is_null($this->bcs))
        {
            $this->bcs = new BlockChainStorageMemory();
        }

        if ($this->blocksNumberToAdd < 1)
        {
            $this->blocksNumberToAdd = 10;
        }

        if (empty($this->key_list))
        {
            $this->key_list = BlockNewFactory::prepareKeyList(10);
        }

        assert(sizeof($this->key_list) > 3);
    }

    /**
     * @return array
     */
    public function getKeyList(): array
    {
        return $this->key_list;
    }

    /**
     * @return bool
     */
    public function isMining(): bool
    {
        return $this->is_mining;
    }

    /**
     * @return int
     */
    public function getMiningAward(): int
    {
        return $this->mining_award;
    }

    /**
     * @return string
     */
    public function getDifficulty(): string
    {
        return $this->difficulty;
    }


    public function produce()
    {
        for ($i = 0; $i < $this->blocksNumberToAdd; $i++)
        {
            $this->addBlock();
        }
    }

    public function getStorage()
    {
        return $this->bcs;
    }

    public function getBalance()
    {
        return $this->balance;
    }

    public function addBlock()
    {
        $this->refreshBalance();
        $this->prepareTrx();

        $mining_key = $this->findKeyByPublicKey($this->getRandomPublicKey());

        $lastBlock = $this->bcs->getLast();

        $bl = new BlockNew([
            'id' => $lastBlock ? $lastBlock->nextBlockId() : 1,
            'prev_block_hash' => $lastBlock ? $lastBlock->hash : BlockExists::EmptyPrevBlockHash,
            'trx' => $this->trx,
            'difficulty' => $this->difficulty,
            'is_mining' => $this->is_mining,
            'mining_private_key' => $mining_key['private_key'],
            'mining_award' => $this->mining_award,
        ]);

        $bl->findProof();

        $this->bcs->store($bl);
    }

    public function refreshBalance() : void
    {
        $this->balance = [];
        foreach ($this->key_list as $key)
        {
            $this->balance[$key['public_key']] = $this->bcs->balance($key['public_key']);
        }
    }

    private function prepareTrx() : void
    {
        $positive_balance = [];
        foreach ($this->balance as $key => $val)
        {
            if ($val > 0)
            {
                $positive_balance[] = $key;
            }
        }

        if (empty($positive_balance))
        {
            $this->trx = [];
            return;
        }

        $trx = [];
        foreach ($positive_balance as $public_key)
        {
            if (mt_rand(0, 100) < 20)
            {
                if ($this->balance[$public_key] > 5)
                {
                    $amount = (int)ceil($this->balance[$public_key] / mt_rand(3, 10));
                    assert($amount > 0);

                    $to = $this->getRandomPublicKey($public_key);
                    $from = $this->findKeyByPublicKey($public_key);

                    $trx[] = new TrxNew([
                        'private_key' => $from['private_key'],
                        'to' => $to,
                        'amount' => $amount,
                        'created_at' => time(),
                        'ttl' => 3600,
                    ]);
                }
            }
        }

        $this->trx = $trx;
    }

    public function getRandomPublicKey($not_this_public_key = '') : string
    {
        $sizeof = sizeof($this->key_list);

        $i = 0;
        while (true)
        {
            $i++;
            if ($i >= 1000)
            {
                throw new Exception('Can not getRandomPublicKey ');
            }

            $rand = mt_rand(0, $sizeof - 1);

            $key = $this->key_list[$rand];
            if ($key['public_key'] != $not_this_public_key)
            {
                return $key['public_key'];
            }
        }

    }

    public function findKeyByPublicKey($public_key)
    {
        foreach ($this->key_list as $key)
        {
            if ($key['public_key'] == $public_key)
            {
                return $key;
            }
        }
        throw new Exception('Can not find key');
    }





}