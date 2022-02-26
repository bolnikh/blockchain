<?php

declare(strict_types=1);

namespace Tests\Unit;

use Domain\Node;
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

        $this->storage->deleteAll('I am sure to delete all trx');
    }

    public function test_store()
    {
        $this->assertEmpty($this->storage->getList());

        $node1 = new Node([
            'ip' => '127.0.0.1',
            'port' => '8000',
            'active' => true,
            'last_active_at' => 1000,
        ]);

        $this->storage->store($node1);

        $this->assertEquals([$node1], $this->storage->getList());

        $node2 = new Node([
            'ip' => '127.0.0.1',
            'port' => '8001',
            'active' => true,
            'last_active_at' => 2000,
        ]);
        $this->storage->store($node2);

        $this->assertEquals([$node1, $node2], $this->storage->getList());

        $this->storage->delete($node1);

        $this->assertEquals([$node2], $this->storage->getList());

        $node3 = new Node([
            'ip' => '127.0.0.1',
            'port' => '8002',
            'active' => false,
            'last_active_at' => 3000,
        ]);
        $this->storage->store($node3);

        $this->assertEquals([$node2, $node3], $this->storage->getList());
    }
}