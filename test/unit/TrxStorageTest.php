<?php

namespace Tests\Unit;

use Domain\Exceptions\TrxNotExists;
use Domain\Factory\TrxNewFactory;
use Domain\Storages\TrxStorageFile;
use Domain\Storages\TrxStorageMemory;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '\..\..\vendor\autoload.php';



class TrxStorageTest extends TestCase
{
    private $storage;
    private $factory;

    public function setUp(): void
    {
        parent::setUp();

        $this->storage = new TrxStorageMemory();
        $this->factory = new TrxNewFactory();

        $this->storage->deleteAll('I am sure to delete all trx');
    }

    public function test_store()
    {
        $tnx = $this->factory->produce();

        $this->assertFalse($this->storage->isExists($tnx->hash));

        $this->assertEmpty($this->storage->getKeyList());

        // -------

        $this->storage->store($tnx);

        $this->assertTrue($this->storage->isExists($tnx->hash));

        $trx_get = $this->storage->get($tnx->hash);

        $this->assertEquals($trx_get->from, $tnx->from);
        $this->assertEquals($trx_get->to, $tnx->to);
        $this->assertEquals($trx_get->amount, $tnx->amount);
        $this->assertEquals($trx_get->hash, $tnx->hash);

        $this->assertEquals([$tnx->hash], $this->storage->getKeyList());

        // --------

        $tnx_1 = $this->factory->reset()->produce();
        $this->storage->store($tnx_1);

        $trx_get_1 = $this->storage->get($tnx_1->hash);
        $this->assertEquals($trx_get_1->hash, $tnx_1->hash);

        $a = [$tnx->hash, $tnx_1->hash];
        sort($a);
        $this->assertEquals($a, $this->storage->getKeyList());

        // -----------

        $this->storage->delete($tnx->hash);
        $this->assertFalse($this->storage->isExists($tnx->hash));
        $this->assertEquals([$tnx_1->hash], $this->storage->getKeyList());
    }

    public function test_not_exist_exception()
    {
        $tnx = $this->factory->reset()->produce();
        $this->expectException(TrxNotExists::class);
        $this->storage->get($tnx->hash);


    }


    public function test_delete_all()
    {
        $tnx = $this->factory->reset()->produce();
        $this->storage->store($tnx);
        $tnx_1 = $this->factory->reset()->produce();
        $this->storage->store($tnx_1);

        $a = [$tnx->hash, $tnx_1->hash];
        sort($a);
        $this->assertEquals($a, $this->storage->getKeyList());

        $this->storage->deleteAll('I am sure to delete all trx');
        $this->assertEmpty($this->storage->getKeyList());
    }


    public function test_delete_all_by_ttl()
    {
        $tnx = $this->factory->reset()->produce();
        $this->storage->store($tnx);

        $factory = $this->factory->reset();
        $factory->created_at = time() - 36000;
        $tnx_1 = $factory->produce();
        $this->storage->store($tnx_1);

        $a = [$tnx->hash, $tnx_1->hash];
        sort($a);
        $this->assertEquals($a, $this->storage->getKeyList());

        $this->storage->deleteAllByTtl();
        $this->assertEquals([$tnx->hash], $this->storage->getKeyList());
    }
}