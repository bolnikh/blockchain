<?php

declare(strict_types = 1);

namespace Domain;

class BlockExists
{
    const EmptyPrevBlockHash = '0';

    public int $id;
    public string $hash;
    public string $prev_block_hash;
    public int $created_at;
    public string $transactions_hash;
    /**
     * @var TransactionExists[]|TransactionNew[]
     */
    public array $transactions;
    public string $difficulty;
    public int $proof;

    private array $fillable = [
        'id', 'hash', 'prev_block_hash', 'created_at', 'transactions_hash', 'transactions', 'difficulty', 'proof',
    ];

    public function __construct($data = []) {

        foreach ($data as $k => $v) {
            if (in_array($k, $this->fillable)) {
                $this->$k = $v;
            }
        }

    }


    public function verifyProof() : bool
    {
        return Hash::getHash($this->hash . $this->proof) <= $this->difficulty;
    }


    public function getTransactionsHash() : string
    {
        $str = '';
        foreach ($this->transactions as $tr)
        {
            $str .= $tr->hash;
        }
        return Hash::getHash($str);
    }

    public function verifyTransactionsHash() : bool
    {
        return $this->transactions_hash === $this->getTransactionsHash();
    }

    public function verifyTransactions() : bool
    {
        foreach ($this->transactions as $tr)
        {
            if (false === $tr->isValidForExistsBlock()) {
                return false;
            }
        }
        return true;
    }


    public function verifyAllTransactions() : bool
    {
        return $this->verifyTransactions();
    }

    public function calcHash() : string
    {
        return Hash::getHash(
            $this->id
            .$this->prev_block_hash
            .$this->created_at
            .$this->transactions_hash
            .$this->difficulty
        );
    }

    public function verifyHash() : bool
    {
        return $this->hash == $this->calcHash();
    }

    public function getHash() : string
    {
        return $this->hash;
    }

    public function nextBlockId()
    {
        return $this->id + 1;
    }

    public function checkMiningTransaction($useMining, $award = 100)
    {
        if ($useMining) {
            if (sizeof($this->transactions) == 0)
            {
                return false; // должна быть хотя бы майнинг транзакция
            }

            $tr_0 = $this->transactions[0];
            if (!$tr_0->isMining())
            {
                return false; // манинг транзакция должна быть первой
            }
            if ($tr_0->amount != $award)
            {
                return false; // манинг транзакция должна быть на верную сумму
            }

            for ($i = 1; $i < sizeof($this->transactions); $i++)
            {
                if ($this->transactions[$i]->isMining())
                {
                    return false; // дожна быть только одна майнинг транзакция
                }
            }
        } else {
            for ($i = 0; $i < sizeof($this->transactions); $i++)
            {
                if ($this->transactions[$i]->isMining())
                {
                    return false; // не должно быть майнинг транзакций
                }
            }
        }

        return true;
    }

    public function verifyBlock()
    {
        return $this->verifyProof()
            && $this->verifyTransactionsHash()
            && $this->verifyTransactions()
            && $this->verifyHash();
    }
}