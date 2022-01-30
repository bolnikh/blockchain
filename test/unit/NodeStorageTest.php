<?php

declare(strict_types=1);

namespace Tests\Unit;

use Domain\Storages\NodeStorageFile;
use Domain\Storages\NodeStorageMemory;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '\..\..\vendor\autoload.php';

class NodeStorageTest extends TestCase
{
    private $storage;

    public function setUp(): void
    {
        parent::setUp();

        $this->storage = new NodeStorageMemory();
        //$this->storage = new NodeStorageFile();

        $this->storage->deleteAll('I am sure to delete all transactions');
    }

    public function test_store()
    {
        $this->assertEmpty($this->storage->getList());

        $node1 = 'node1';
        $this->storage->store($node1);

        $this->assertEquals([$node1], $this->storage->getList());

        $node2 = 'node2';
        $this->storage->store($node2);

        $this->assertEquals([$node1, $node2], $this->storage->getList());

        $this->storage->delete($node1);

        $this->assertEquals([$node2], $this->storage->getList());

        $node3 = 'aaa';
        $this->storage->store($node3);

        $this->assertEquals([$node3, $node2], $this->storage->getList());
    }
}