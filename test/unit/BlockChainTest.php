<?php

namespace Tests\Unit;

require_once __DIR__ . '\..\..\vendor\autoload.php';

use Domain\Storages\BlockChainStorageMemory;
use Domain\BlockNew;
use Domain\Factory\BlockNewFactory;
use Domain\Factory\BlockMiningFactory;
use Domain\TransactionNew;
use PHPUnit\Framework\TestCase;

class BlockChainTest extends TestCase
{
    public function test_create_blockchain()
    {
        $difficulty = '0f';
        $is_mining = true;
        $mining_award = 100;

        $key_list = BlockNewFactory::prepareKeyList(10);
        $bsm = new BlockChainStorageMemory();

        $nb1 = new BlockNew([
            'id' => 1,
            'prev_block_hash' => '0',
            'transactions' => [],
            'difficulty' => $difficulty,
            'is_mining' => $is_mining,
            'mining_private_key' => $key_list[0]['private_key'],
            'mining_award' => $mining_award,
        ]);

        $bsm->store($nb1);
        $this->assertEquals($nb1, $bsm->getById(1));
        $this->assertEquals($nb1, $bsm->getByHash($nb1->getHash()));
        $this->assertEquals($mining_award, $bsm->balance($key_list[0]['public_key']));



        $nb2 = new BlockNew([
            'id' => 2,
            'prev_block_hash' => $nb1->getHash(),
            'transactions' => [],
            'difficulty' => $difficulty,
            'is_mining' => $is_mining,
            'mining_private_key' => $key_list[0]['private_key'],
            'mining_award' => $mining_award,
        ]);

        $bsm->store($nb2);
        $this->assertEquals($nb2, $bsm->getById(2));
        $this->assertEquals($nb2, $bsm->getByHash($nb2->getHash()));

        $this->assertEquals($nb1, $bsm->getFirst());
        $this->assertEquals($nb2, $bsm->getLast());
        $this->assertEquals($nb2, $bsm->getNext(1));
        $this->assertEquals($nb1, $bsm->getPrev(2));
        $this->assertEquals(2, $bsm->getMaxId());

        $this->assertEquals($mining_award * 2, $bsm->balance($key_list[0]['public_key']));


        $tn1 = new TransactionNew([
            'private_key' => $key_list[0]['private_key'],
            'to' => $key_list[1]['public_key'],
            'amount' => $mining_award / 5,
        ]);

        $tn2 = new TransactionNew([
            'private_key' => $key_list[0]['private_key'],
            'to' => $key_list[2]['public_key'],
            'amount' => $mining_award / 10,
        ]);

        $nb3 = new BlockNew([
            'id' => 3,
            'prev_block_hash' => $nb2->getHash(),
            'transactions' => [
                $tn1, $tn2
            ],
            'difficulty' => $difficulty,
            'is_mining' => $is_mining,
            'mining_private_key' => $key_list[1]['private_key'],
            'mining_award' => $mining_award,
        ]);

        $bsm->store($nb3);
        $this->assertEquals($nb3, $bsm->getById(3));
        $this->assertEquals($nb3, $bsm->getByHash($nb3->getHash()));


        $this->assertEquals($mining_award * 2
            - ($mining_award / 5)
            - ($mining_award / 10)
            , $bsm->balance($key_list[0]['public_key']));

        $this->assertEquals($mining_award + $mining_award / 5, $bsm->balance($key_list[1]['public_key']));
        $this->assertEquals($mining_award / 10, $bsm->balance($key_list[2]['public_key']));



    }
}