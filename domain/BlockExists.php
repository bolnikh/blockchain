<?php

declare(strict_types = 1);

namespace Domain;

class BlockExists
{
    public int $id;
    public string $hash;
    public string $prev_block_hash;
    public int $created_at;
    public string $transactions_hash;
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

    public function verifyTransaction() : bool
    {
        foreach ($this->transactions as $tr)
        {
            if (false === $tr->isValid()) {
                return false;
            }
            if (false === $this->checkBalance($tr))
            {
                return false;
            }
        }
        return true;
    }

    public function checkBalance(TransactionExists $tr) : bool
    {
        return true;
    }

    public function verifyAllTransactions() : bool
    {
        foreach ($this->transactions as $tr)
        {
            if (false === $this->verifyTransaction($tr))
            {
                return false;
            }
        }
        return true;
    }

    public function calcHash() : string
    {
        return Hash::getHash(
            $this->id
            .$this->prev_block_hash
            .$this->created_at
            .$this->transactions_hash
            .$this->difficulty
            .$this->proof
        );
    }

    public function verifyHash() : bool
    {
        return $this->hash == $this->calcHash();
    }
}