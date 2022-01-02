<?php

declare(strict_types = 1);

namespace Domain;

class BlockNew extends BlockExists
{
    private string $mining_private_key;
    private bool $is_mining =  true;
    private int $mining_award = 10;

    private array $fillable = [
        'id', 'prev_block_hash', 'created_at', 'transactions', 'difficulty', 'mining_private_key', 'is_mining', 'mining_award'
    ];

    public function __construct($data = []) {
        foreach ($data as $k => $v) {
            if (in_array($k, $this->fillable)) {
                $this->$k = $v;
            }
        }

        if (empty($this->created_at))
        {
            $this->created_at = time();
        }

        $this->createMiningAwardTransaction();
        $this->transactions_hash = $this->getTransactionsHash();
        $this->hash = $this->calcHash();

    }


    public function createMiningAwardTransaction() : void
    {
        if (!$this->is_mining || $this->mining_award == 0)
        {
            return ;
        }

        $tr = new TransactionMining(
            [
                'amount' => $this->mining_award,
                'private_key' => $this->mining_private_key,
            ]
        );

        array_unshift($this->transactions, $tr);
    }


    public function filterValidTransactions() : bool
    {
        foreach ($this->transactions as $k => $tr)
        {
            if (false === $tr->isValidForNewBlock()) {
                unset($this->transactions[$k]);
                break;
            }
        }
        return true;
    }

    public function findProof($from = 0, $to = 1000000) : bool
    {
        for ($i = $from; $i < $to; $i++) {
            $this->proof = $i;
            if ($this->verifyProof())
            {
                return true;
            }
        }
        return false;
    }

    public function addTransaction(TransactionNew $tr) : void
    {
        $this->transactions[] = $tr;
    }

    public function verifyTransactions() : bool
    {
        foreach ($this->transactions as $tr)
        {
            if (false === $tr->isValidForNewBlock()) {
                return false;
            }
        }
        return true;
    }
}