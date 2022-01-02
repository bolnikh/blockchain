<?php

namespace Tests\Unit;


use Domain\BlockNew;
use Domain\Factory\BlockChainFactory;
use Domain\Storages\BlockChainStorageUnion;
use Domain\TransactionNew;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '\..\..\vendor\autoload.php';

class BlockChainUnionTest extends TestCase
{

    public function test_union()
    {
        $bcf = new BlockChainFactory();

        $bcf->produce();
        $st = $bcf->getStorage();


        $newBlocks = [];
        $bcsMaxId = $st->getMaxId();

        $newBlocks[] = $st->getById($bcsMaxId - 1);
        $bcsu_1 = new BlockChainStorageUnion($st, $newBlocks);
        $this->assertEquals(BlockChainStorageUnion::NO_NEED_MERGE, $bcsu_1->merge());


        $newBlocks[] = $st->getById($bcsMaxId);
        $bcsu_2 = new BlockChainStorageUnion($st, $newBlocks);
        $this->assertEquals(BlockChainStorageUnion::NO_NEED_MERGE, $bcsu_2->merge());


        $keyList = $bcf->getKeyList();

        $key1 = $keyList[$shift = mt_rand(0,3)];
        $key2 = $keyList[$shift += mt_rand(0,3)];
        $key3 = $keyList[$shift + mt_rand(0,3)];

        $lastBl = $st->getLast();

        $newTr = new TransactionNew([
            'private_key' => $key2['private_key'],
            'to' => $key3['public_key'],
            'amount' => 10000000, // столько точно нет
            'created_at' => time(),
            'ttl' => 3600,
        ]);

        $newBl = new BlockNew([
            'id' => $lastBl->id + 1,
            'prev_block_hash' => $lastBl->hash,
            'transactions' => [$newTr],
            'difficulty' => $bcf->getDifficulty(),
            'is_mining' => $bcf->isMining(),
            'mining_private_key' => $key1['private_key'],
            'mining_award' => $bcf->getMiningAward(),
        ]);

        $newBl->findProof();

        $newBlocks[] = $newBl;
        $bcsu_3 = new BlockChainStorageUnion($st, $newBlocks);
        $this->assertEquals(BlockChainStorageUnion::ERROR_VALIDATE_NEW_BLOCKS, $bcsu_3->merge());


    }

    public function test_union_success()
    {
        $bcf = new BlockChainFactory();

        $bcf->produce();
        $st = $bcf->getStorage();


        $newBlocks = [];
        $bcsMaxId = $st->getMaxId();

        $newBlocks[] = $st->getById($bcsMaxId - 1);
        $newBlocks[] = $st->getById($bcsMaxId);




        $keyList = $bcf->getKeyList();

        $key1 = $keyList[mt_rand(0,3)];

        $lastBl = $st->getLast();



        $positive_balance = [];
        $bcf->refreshBalance();
        foreach ($bcf->getBalance() as $key => $val)
        {
            if ($val > 0)
            {
                $positive_balance[$key] = $val;
            }
        }

        $this->assertLessThan(sizeof($positive_balance), 3);

        $rand_keys = array_rand($positive_balance, 2);

        $key_from_private = $bcf->findKeyByPublicKey($key_from_public = $rand_keys[0])['private_key'];

        $key_to = $rand_keys[1];
        $balance_from = $positive_balance[$key_from_public];

        $newTr1 = new TransactionNew([
            'private_key' => $key_from_private,
            'to' => $key_to,
            'amount' => intval(ceil($balance_from / 4)),
            'created_at' => time(),
            'ttl' => 3600,
        ]);


        $newBl1 = new BlockNew([
            'id' => $lastBl->id + 1,
            'prev_block_hash' => $lastBl->hash,
            'transactions' => [$newTr1],
            'difficulty' => $bcf->getDifficulty(),
            'is_mining' => $bcf->isMining(),
            'mining_private_key' => $key1['private_key'],
            'mining_award' => $bcf->getMiningAward(),
        ]);
        $newBl1->findProof();


        $newTr2 = new TransactionNew([
            'private_key' => $key_from_private,
            'to' => $key_to,
            'amount' => intval(ceil($balance_from / 3)),
            'created_at' => time(),
            'ttl' => 3600,
        ]);


        $newBl2 = new BlockNew([
            'id' => $newBl1->id + 1,
            'prev_block_hash' => $newBl1->hash,
            'transactions' => [$newTr2],
            'difficulty' => $bcf->getDifficulty(),
            'is_mining' => $bcf->isMining(),
            'mining_private_key' => $key1['private_key'],
            'mining_award' => $bcf->getMiningAward(),
        ]);
        $newBl2->findProof();


        $newBlocks[] = $newBl1;
        $newBlocks[] = $newBl2;

        $bcsu = new BlockChainStorageUnion($st, $newBlocks);
        $this->assertEquals(BlockChainStorageUnion::MERGED, $bcsu->merge());

        $lastBl = $st->getLast();
        $prevBl = $st->getPrev($lastBl->id);

        $this->assertEquals($lastBl->id, $newBl2->id);
        $this->assertEquals($lastBl->hash, $newBl2->hash);

        $this->assertEquals($prevBl->id, $newBl1->id);
        $this->assertEquals($prevBl->hash, $newBl1->hash);

    }

}