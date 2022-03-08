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
    public string $trx_hash;
    /**
     * @var TrxExists[]|TrxNew[]
     */
    public array $trx;
    public string $difficulty;
    public int $proof;

    private array $fillable = [
        'id', 'hash', 'prev_block_hash', 'created_at', 'trx_hash', 'trx', 'difficulty', 'proof',
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


    public function getTrxHash() : string
    {
        $str = '';
        foreach ($this->trx as $tr)
        {
            $str .= $tr->hash;
        }
        return Hash::getHash($str);
    }

    public function verifyTrxHash() : bool
    {
        return $this->trx_hash === $this->getTrxHash();
    }

    public function verifyTrx() : bool
    {
        foreach ($this->trx as $tr)
        {
            if (false === $tr->isValidForExistsBlock()) {
                return false;
            }
        }
        return true;
    }


    public function verifyAllTrx() : bool
    {
        return $this->verifyTrx();
    }

    public function calcHash() : string
    {
        return Hash::getHash(
            $this->id
            .$this->prev_block_hash
            .$this->created_at
            .$this->trx_hash
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

    public function checkMiningTrx($useMining, $award = 100)
    {
        if ($useMining) {
            if (sizeof($this->trx) == 0)
            {
                return false; // должна быть хотя бы майнинг транзакция
            }

            $tr_0 = $this->trx[0];
            if (!$tr_0->isMining())
            {
                return false; // манинг транзакция должна быть первой
            }
            if ($tr_0->amount != $award)
            {
                return false; // манинг транзакция должна быть на верную сумму
            }

            for ($i = 1; $i < sizeof($this->trx); $i++)
            {
                if ($this->trx[$i]->isMining())
                {
                    return false; // дожна быть только одна майнинг транзакция
                }
            }
        } else {
            for ($i = 0; $i < sizeof($this->trx); $i++)
            {
                if ($this->trx[$i]->isMining())
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
            && $this->verifyTrxHash()
            && $this->verifyTrx()
            && $this->verifyHash();
    }

    public function hasTrx(string $trxHash) : bool
    {
        foreach ($this->trx as $tr)
        {
            if ($tr->hash === $trxHash) {
                return true;
            }
        }
        return false;
    }

    public function toJson() : string
    {
        return json_encode($this);
    }

    public static function fromJson(string $json_data) : BlockExists
    {
        $bn_arr = json_decode($json_data, true, 10, JSON_THROW_ON_ERROR);

        $trans = [];
        foreach ($bn_arr['trx'] as $tr)
        {
            $trans[] = new TrxExists($tr);
        }

        $bn_arr['trx'] = $trans;

        return new BlockExists($bn_arr);
    }
}