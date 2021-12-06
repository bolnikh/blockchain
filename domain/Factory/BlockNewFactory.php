<?php


declare(strict_types=1);

namespace Domain\Factory;

use Domain\BlockNew;
use Domain\TransactionMining;
use Domain\TransactionNew;
use Domain\KeyMaster;
use Domain\TransactionString;

class BlockNewFactory
{
    private array $key_list_from = [];
    private int $key_list_from_size = 10;

    private array $key_list_to = [];
    private int $key_list_to_size = 10;

    private array $transaction_list = [];
    private int $transaction_list_size = 5;

    private int $id = 1;
    private string $prev_block_hash = '0';
    private array $transactions = [];
    private string $difficulty = '000f';

    private string $mining_private_key;
    private bool $is_mining = true;
    private int $mining_award = 100;



    public function __set($key, $val)
    {
        $this->$key = $val;
    }

    public function __get($key)
    {
        return $this->$key;
    }

    public static function prepareKey() : array
    {
        $km = new KeyMaster();
        $km->generateKey();

        return [
            'km' => $km,
            'private_key' => $km->getPrivateKey(),
            'public_key' => $km->getPublicKey(true),
        ];
    }

    public static function prepareKeyList($size) : array
    {
        $key_list = [];
        for ($i = 0; $i < $size; $i++) {
            $key_list[] = self::prepareKey();
        }

        return $key_list;
    }

    private function arrayRand($array) : array
    {
        return $array[array_rand($array)];
    }

    protected function prepareTransactionList() : void
    {
        $this->key_list_from = self::prepareKeyList($this->key_list_from_size);
        $this->key_list_to = self::prepareKeyList($this->key_list_to_size);
        $this->mining_key = self::prepareKey();

        for ($i = 0; $i < $this->transaction_list_size; $i++)
        {
            $tnf = new TransactionNewFactory();

            $from = $this->arrayRand($this->key_list_from);
            $tnf->private_key_from = $from['private_key'];

            $to = $this->arrayRand($this->key_list_to);
            $tnf->to = $to['public_key'];

            $tnf->amount = mt_rand(1, 10) * 10;

            $tn = $tnf->produce();

            $this->transaction_list[] = $tn;
        }

    }

    private function prepare() : void
    {
        $this->prepareTransactionList();

        if (empty($this->mining_private_key))
        {
            $key = self::prepareKey();
            $this->mining_private_key = $key['private_key'];
        }
    }

    public function produce() : BlockNew
    {
        $this->prepare();

        $nb = new BlockNew([
            'id' => $this->id,
            'prev_block_hash' => $this->prev_block_hash,
            'transactions' => $this->transactions,
            'difficulty' => $this->difficulty,
            'is_mining' => $this->is_mining,
            'mining_private_key' => $this->mining_private_key,
            'mining_award' => $this->mining_award,
        ]);

        $step = 10000;
        for ($i = 0; $i < 1000; $i++)
        {
            if ($nb->findProof($i * $step, ($i+1) * $step))
            {
                break;
            }
        }

        return $nb;
    }
}