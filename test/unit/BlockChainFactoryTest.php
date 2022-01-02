<?php

namespace Tests\Unit;


use Domain\BlockChainBalanceValidate;
use Domain\BlockNew;
use Domain\Factory\BlockChainFactory;
use Domain\TransactionNew;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '\..\..\vendor\autoload.php';

class BlockChainFactoryTest  extends TestCase
{

    public function test_create_bc()
    {
        $bcbv = new BlockChainBalanceValidate();
        $bcf = new BlockChainFactory();

        $bcf->produce();

        $st = $bcf->getStorage();

        $firstBlock = $st->getFirst();
        $this->assertEquals(1, $firstBlock->id);

        $this->assertEquals(10, $st->getMaxId());

        foreach ($st as $bl)
        {
            $this->assertTrue($bl->verifyProof());
            $this->assertTrue($bl->verifyTransactions());
            $this->assertTrue($bl->verifyHash());
            $this->assertTrue($bcbv->validateBlockTransactionsBalance($st, $bl));
            $this->assertTrue($bl->checkMiningTransaction($bcf->isMining(), $bcf->getMiningAward()));
        }

        // create new block and add & validate new transactions

        $keyList = $bcf->getKeyList();

        $key1 = $keyList[$shift = mt_rand(0,3)];
        $key2 = $keyList[$shift += mt_rand(0,3)];
        $key3 = $keyList[$shift + mt_rand(0,3)];


        $lastBl = $st->getLast();

        $newBl = new BlockNew([
            'id' => $lastBl->id + 1,
            'prev_block_hash' => $lastBl->hash,
            'transactions' => [],
            'difficulty' => $bcf->getDifficulty(),
            'is_mining' => $bcf->isMining(),
            'mining_private_key' => $key1['private_key'],
            'mining_award' => $bcf->getMiningAward(),
        ]);

        $newTr = new TransactionNew([
            'private_key' => $key2['private_key'],
            'to' => $key3['public_key'],
            'amount' => 10000000, // столько точно нет
            'created_at' => time(),
            'ttl' => 3600,
        ]);

        $this->assertFalse($bcbv->validateNewTransactionBalance($st, $newBl, $newTr));


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

        $newTr = new TransactionNew([
            'private_key' => $key_from_private,
            'to' => $key_to,
            'amount' => intval(ceil($balance_from / 2)),
            'created_at' => time(),
            'ttl' => 3600,
        ]);

        $this->assertTrue($bcbv->validateNewTransactionBalance($st, $newBl, $newTr));


        $newTr = new TransactionNew([
            'private_key' => $key_from_private,
            'to' => $key_to,
            'amount' => $balance_from * 2,
            'created_at' => time(),
            'ttl' => 3600,
        ]);

        $this->assertFalse($bcbv->validateNewTransactionBalance($st, $newBl, $newTr));


        $newTr = new TransactionNew([
            'private_key' => $key_from_private,
            'to' => $key_to,
            'amount' => intval(floor($balance_from / 2)),
            'created_at' => time(),
            'ttl' => 3600,
        ]);

        $newBl->addTransaction($newTr);

        $newTr_1 = new TransactionNew([
            'private_key' => $key_from_private,
            'to' => $key_to,
            'amount' => intval(floor($balance_from / 2)),
            'created_at' => time(),
            'ttl' => 3600,
        ]);

        $this->assertTrue($bcbv->validateNewTransactionBalance($st, $newBl, $newTr_1));

        $newBl->addTransaction($newTr_1);

        $newTr_2 = new TransactionNew([
            'private_key' => $key_from_private,
            'to' => $key_to,
            'amount' => intval(floor($balance_from / 2)),
            'created_at' => time(),
            'ttl' => 3600,
        ]);

        $this->assertFalse($bcbv->validateNewTransactionBalance($st, $newBl, $newTr_2));

    }
}

