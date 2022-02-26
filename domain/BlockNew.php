<?php

declare(strict_types = 1);

namespace Domain;

class BlockNew extends BlockExists
{
    private string $mining_private_key;
    private bool $is_mining =  true;
    private int $mining_award = 10;

    private array $fillable = [
        'id', 'prev_block_hash', 'created_at', 'trx', 'difficulty', 'mining_private_key', 'is_mining', 'mining_award'
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
        $this->trx_hash = $this->getTrxHash();
        $this->hash = $this->calcHash();

    }


    public function createMiningAwardTransaction() : void
    {
        if (!$this->is_mining || $this->mining_award == 0)
        {
            return ;
        }

        $tr = new TrxMining(
            [
                'amount' => $this->mining_award,
                'private_key' => $this->mining_private_key,
            ]
        );

        array_unshift($this->trx, $tr);
    }


    public function filterValidTrx() : bool
    {
        foreach ($this->trx as $k => $tr)
        {
            if (false === $tr->isValidForNewBlock()) {
                unset($this->trx[$k]);
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

    public function addTrx(TrxNew $tr) : void
    {
        $this->trx[] = $tr;
    }

    public function verifyTrx() : bool
    {
        foreach ($this->trx as $tr)
        {
            if (false === $tr->isValidForNewBlock()) {
                return false;
            }
        }
        return true;
    }
}