<?php

declare(strict_types = 1);

namespace Domain;

class BlockNew extends BlockExists
{
    private string $private_key;
    private bool $is_mining =  true;
    private int $mining_award = 10;

    private array $fillable = [
        'id', 'prev_block_hash', 'created_at', 'transactions', 'difficulty', 'private_key', 'is_mining', 'mining_award'
    ];

    public function __construct($data = []) {
        foreach ($data as $k => $v) {
            if (in_array($k, $this->fillable)) {
                $this->$k = $v;
            }
        }
    }


    public function createMiningAwardTransaction() : ?TransactionNew
    {
        if (!$this->is_mining || $this->mining_award == 0)
        {
            return false;
        }

        $tr = new TransactionNew(
            [
                'to' => $this->to,
                'amount' => $this->mining_award,
                'created_at' => $this->created_at,
                'ttl' => $this->ttl,
                'private_key' => $this->private_key,
            ]
        );

        array_unshift($this->transactions, $tr);
    }


    public function filterValidTransactions() : bool
    {
        foreach ($this->transactions as $k => $tr)
        {
            if (false === $tr->isValid()) {
                unset($this->transactions[$k]);
                break;
            }
            if (false === $this->checkBalance($tr))
            {
                unset($this->transactions[$k]);
                break;
            }
        }
        return true;
    }

    public function createBlock()
    {
        $tr_hash = $this->getTransactionsHash();
    }
}