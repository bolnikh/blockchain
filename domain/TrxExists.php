<?php

declare(strict_types = 1);

namespace Domain;

/**
 * Class TrxExists
 * транзакция в блоке
 * @package Domain
 */
class TrxExists
{
    public string $from;
    public string $to;
    public int $amount;
    public int $created_at = 0;
    public int $ttl = 24*3600;
    public string $sign;
    public string $hash;

    protected $fillable = [
        'from', 'to', 'amount', 'created_at', 'ttl', 'sign', 'hash',
    ];

    const MINING_FROM = '0';

    public function __construct($data = []) {

        foreach ($data as $k => $v) {
            if (in_array($k, $this->fillable)) {
                $this->$k = $v;
            }
        }

        if (empty($this->created_at)) {
            $this->created_at = time();
        }
        if (empty($this->ttl)) {
            $this->ttl = 24*3600;
        }
    }

    /**
     * Строка данных транзакции для подписи
     * @return string
     */
    public function makeString() : string
    {
        return $this->from
            .':'
            .$this->to
            .':'
            .$this->amount
            .':'
            .$this->created_at
            .':'
            .$this->ttl
            ;
    }


    public function verifySign() : bool
    {
        if ($this->isMining()) {
            return Sign::check($this->makeString(), $this->sign, $this->to);
        } else {
            return Sign::check($this->makeString(), $this->sign, $this->from);
        }
    }

    public function calcHash() : string
    {
        return Hash::getHash($this->makeString() . ':' . $this->sign );
    }

    public function verifyHash() : bool
    {
        return $this->hash == $this->calcHash();
    }

    public function verifyTtl() : bool
    {
        return $this->created_at + $this->ttl >= time();
    }


    public function isBaseValid() : bool
    {
        return $this->verifyHash() && $this->verifySign();
    }

    /**
     * Валиден в блоке
     * @return bool
     */
    public function isValidForExistsBlock() : bool
    {
        return $this->isBaseValid();
    }

    /**
     * Можно добавить в новый блок
     * @return bool
     */
    public function isValidForNewBlock() : bool
    {
        return $this->verifyTtl() && $this->isBaseValid();
    }


    /**
     * Строковое представление транзакции
     * @return string
     */
    public function toString() : string
    {
        return $this->makeString() . ':' . $this->sign . ':' . $this->hash;
    }


    public function isMining()
    {
        return $this->from === TrxExists::MINING_FROM;
    }
}