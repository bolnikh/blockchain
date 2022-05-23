<?php

namespace Tests\Unit;


use Domain\Storages\FileKeyStorage;
use Domain\Storages\MemoryKeyStorage;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '\..\..\vendor\autoload.php';


class FileKeyStorageTest extends TestCase
{

    private $faker;

    public function setUp(): void
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();
    }


    public function test_all()
    {
        $dir = __DIR__.'/../../storage/test';
        if (!file_exists($dir)) {
            mkdir($dir);
        }

        $fksArr[] = new FileKeyStorage($dir, '', 'json');
        $fksArr[] = new FileKeyStorage($dir, '1', 'json');
        $fksArr[] = new FileKeyStorage($dir, '2', 'json');
        $fksArr[] = new FileKeyStorage($dir, '2:2', 'json');
        $fksArr[] = new FileKeyStorage($dir, '2:2:3', 'json');

        foreach ($fksArr as $fks) {
            $fks->deleteAll('I am sure to delete all');

            $key = $this->faker->lexify('???????????');
            $val = $this->faker->sentence();

            $fks->set($key, $val);

            $this->assertTrue($fks->isExists($key));
            $this->assertFalse($fks->isExists($key.'12345'));
            $this->assertEquals($val, $fks->get($key));


            $key1 = $this->faker->lexify('???????????');
            $val1 = $this->faker->sentence();

            $fks->set($key1, $val1);

            $this->assertTrue($fks->isExists($key1));
            $this->assertFalse($fks->isExists($key1.'12345'));
            $this->assertEquals($val1, $fks->get($key1));


            $fks->deleteAll('I am sure to delete all');

            $this->assertFalse($fks->isExists($key));
            $this->assertFalse($fks->isExists($key1));

            $key = $this->faker->lexify('???????????');
            $val = $this->faker->sentence();

            $fks->set($key, $val);

            $this->assertTrue($fks->isExists($key));
            $this->assertFalse($fks->isExists($key.'12345'));
            $this->assertEquals($val, $fks->get($key));

            $this->assertEquals([$key], $fks->list());

            $key1 = $this->faker->lexify('???????????');
            $val1 = $this->faker->sentence();

            $fks->set($key1, $val1);

            $a1 = [$key, $key1];
            $a2 = $fks->list();
            sort($a1);
            sort($a2);
            $this->assertEquals($a1, $a2);


            $fks->delete($key);

            $this->assertEquals([$key1], $fks->list());
            $this->assertFalse($fks->isExists($key));
            $this->assertTrue($fks->isExists($key1));
        }
    }

    public function test_short_keys()
    {
        $dir = __DIR__.'/../../storage/test';
        if (!file_exists($dir)) {
            mkdir($dir);
        }

        $fks = new FileKeyStorage($dir, '2:2', 'json');
        $fks->deleteAll('I am sure to delete all');


        $key = 1;
        $val = $this->faker->sentence();

        $fks->set($key, $val);
        $this->assertTrue($fks->isExists($key));
        $this->assertFalse($fks->isExists($key.'12345'));
        $this->assertEquals($val, $fks->get($key));


        $a1 = ['000001'];
        $a2 = $fks->list();

        $this->assertEquals($a1, $a2);

        $fks->deleteAll('I am sure to delete all');

        $key = '1_0';
        $val = $this->faker->sentence();

        $fks->set($key, $val);
        $this->assertTrue($fks->isExists($key));
        $this->assertFalse($fks->isExists($key.'12345'));
        $this->assertEquals($val, $fks->get($key));

        $key1 = '1_1';
        $val1 = $this->faker->sentence();

        $fks->set($key1, $val1);
        $this->assertTrue($fks->isExists($key1));
        $this->assertFalse($fks->isExists($key1.'12345'));
        $this->assertEquals($val1, $fks->get($key1));

        $a1 = ['0001_0', '0001_1'];
        $a2 = $fks->list();

        $this->assertEquals($a1, $a2);

        $fks->deleteAll('I am sure to delete all');

        $key = '111_0';
        $val = $this->faker->sentence();

        $fks->set($key, $val);
        $this->assertTrue($fks->isExists($key));
        $this->assertFalse($fks->isExists($key.'12345'));
        $this->assertEquals($val, $fks->get($key));

        $key1 = '111_1';
        $val1 = $this->faker->sentence();

        $fks->set($key1, $val1);
        $this->assertTrue($fks->isExists($key1));
        $this->assertFalse($fks->isExists($key1.'12345'));
        $this->assertEquals($val1, $fks->get($key1));

        $a1 = ['0111_0', '0111_1'];
        $a2 = $fks->list();

        $this->assertEquals($a1, $a2);

    }
}