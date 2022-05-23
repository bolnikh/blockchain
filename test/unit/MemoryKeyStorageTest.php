<?php

namespace Tests\Unit;


use Domain\Storages\MemoryKeyStorage;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '\..\..\vendor\autoload.php';


class MemoryKeyStorageTest extends TestCase
{
    private $storage;
    private $faker;

    public function setUp(): void
    {
        parent::setUp();

        $this->storage = new MemoryKeyStorage();

        $this->storage->reset('I am sure to delete all');

        $this->faker = \Faker\Factory::create();
    }

    public function test_all()
    {
        $key = $this->faker->lexify('???????????');
        $val = $this->faker->sentence();

        $this->storage->set($key, $val);

        $this->assertTrue($this->storage->isExists($key));
        $this->assertFalse($this->storage->isExists($key.'12345'));
        $this->assertEquals($val, $this->storage->get($key));



        $key1 = $this->faker->lexify('???????????');
        $val1 = $this->faker->sentence();

        $this->storage->set($key1, $val1);

        $this->assertTrue($this->storage->isExists($key1));
        $this->assertFalse($this->storage->isExists($key1.'12345'));
        $this->assertEquals($val1, $this->storage->get($key1));


        $this->storage->deleteAll('I am sure to delete all');

        $this->assertFalse($this->storage->isExists($key));
        $this->assertFalse($this->storage->isExists($key1));


        $key = $this->faker->lexify('???????????');
        $val = $this->faker->sentence();

        $this->storage->set($key, $val);

        $this->assertTrue($this->storage->isExists($key));
        $this->assertFalse($this->storage->isExists($key.'12345'));
        $this->assertEquals($val, $this->storage->get($key));

        $this->assertEquals([$key], $this->storage->list());

        $key1 = $this->faker->lexify('???????????');
        $val1 = $this->faker->sentence();

        $this->storage->set($key1, $val1);

        $this->assertEquals([$key, $key1], $this->storage->list());


        $this->storage->delete($key);

        $this->assertEquals([$key1], $this->storage->list());
        $this->assertFalse($this->storage->isExists($key));
        $this->assertTrue($this->storage->isExists($key1));
    }
}
