<?php

namespace Tests\Unit;


use Domain\Storages\KeyBranchStorage;
use Domain\Storages\MemoryKeyStorage;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '\..\..\vendor\autoload.php';

class KeyBranchStorageTest extends TestCase
{
    private $faker;

    public function setUp(): void
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();
    }


    public function test_1()
    {
        $dir = __DIR__ . '/../../storage/test';
        if (!file_exists($dir)) {
            mkdir($dir);
        }

        $ks = new MemoryKeyStorage();
        $kbs = new KeyBranchStorage($ks);

        $key = $this->faker->lexify('???????????');
        $val = $this->faker->sentence();

        $branch = 0;

        $kbs->set($key, $branch, $val);

        $this->assertTrue($kbs->isExists($key, $branch));
        $this->assertFalse($kbs->isExists($key . '12345', $branch));
        $this->assertEquals($val, $kbs->get($key, $branch));

        $branch = 1;

        $kbs->set($key, $branch, $val);

        $this->assertTrue($kbs->isExists($key, $branch));
        $this->assertFalse($kbs->isExists($key . '12345', $branch));
        $this->assertEquals($val, $kbs->get($key, $branch));


        // getKeyParts

        $key = "123_345_78";
        $this->assertEquals(['key' => '123_345', 'branch' => 78], $kbs->getKeyParts($key));


        $key1 = $this->faker->lexify('???????????');
        $val1 = $this->faker->sentence();

        $branch1 = 0;

        $kbs->set($key1, $branch1, $val1);

        $this->assertTrue($kbs->isExists($key1, $branch1));
        $this->assertFalse($kbs->isExists($key1 . '12345', $branch1));
        $this->assertEquals($val1, $kbs->get($key1, $branch1));

        // list

        $sort = function ($a, $b) {
            if ($a['key'] != $b['key']) {
                return $a['key'] <=> $b['key'];
            }
            return $a['branch'] <=> $b['branch'];
        };

        $res = [
            ['key' => $key, 'branch' => 0],
            ['key' => $key, 'branch' => 1],
            ['key' => $key1, 'branch' => 0],
        ];

        $res = usort($res, $sort);

        $res_ = $kbs->list();
        $res_ = usort($res_, $sort);

        $this->assertEquals($res, $res_);

        // listBranch

        $res0 = [
            ['key' => $key, 'branch' => 0],
            ['key' => $key1, 'branch' => 0],
        ];
        $res0 = usort($res0, $sort);

        $res1 = [
            ['key' => $key, 'branch' => 1],
        ];
        $res1 = usort($res1, $sort);

        $res0_ = $kbs->listBranch(0);
        $res0_ = usort($res0_, $sort);

        $res1_ = $kbs->listBranch(1);
        $res1_ = usort($res1_, $sort);

        $this->assertEquals($res0, $res0_);
        $this->assertEquals($res1, $res1_);


        // delete
        $kbs->delete($key1, $branch1);

        $this->assertFalse($kbs->isExists($key1, $branch1));


        // deleteAll
        $kbs->deleteAll('I am sure to delete all');

        $this->assertEmpty($kbs->list());

    }

    // deleteBranch
    public function test_2() {

        $sort = function ($a, $b) {
            if ($a['key'] != $b['key']) {
                return $a['key'] <=> $b['key'];
            }
            return $a['branch'] <=> $b['branch'];
        };

        $ks = new MemoryKeyStorage();
        $kbs = new KeyBranchStorage($ks);

        $branch0 = 0;

        $key10 = $this->faker->lexify('???????????');
        $val10 = $this->faker->sentence();
        $kbs->set($key10, $branch0, $val10);

        $key20 = $this->faker->lexify('???????????');
        $val20 = $this->faker->sentence();
        $kbs->set($key20, $branch0, $val20);

        $key30 = $this->faker->lexify('???????????');
        $val30 = $this->faker->sentence();
        $kbs->set($key30, $branch0, $val30);


        $branch1 = 0;

        $key11 = $this->faker->lexify('???????????');
        $val11 = $this->faker->sentence();
        $kbs->set($key11, $branch1, $val11);

        $key21 = $this->faker->lexify('???????????');
        $val21 = $this->faker->sentence();
        $kbs->set($key21, $branch1, $val21);

        $key31 = $this->faker->lexify('???????????');
        $val31 = $this->faker->sentence();
        $kbs->set($key31, $branch1, $val31);



        $listBranch0 = [
            ['key' => $key10, 'branch' => $branch0],
            ['key' => $key20, 'branch' =>$branch0],
            ['key' => $key30, 'branch' =>$branch0],
        ];
        $listBranch0 = usort($listBranch0, $sort);

        $listBranch0_ = $kbs->listBranch($branch0);
        $listBranch0_ = usort($listBranch0_, $sort);

        $this->assertEquals($listBranch0, $listBranch0_);


        $listBranch1 = [
            ['key' => $key11, 'branch' => $branch1],
            ['key' => $key21, 'branch' =>$branch1],
            ['key' => $key31, 'branch' =>$branch1],
        ];
        $listBranch1 = usort($listBranch1, $sort);

        $listBranch1_ = $kbs->listBranch($branch1);
        $listBranch1_ = usort($listBranch1_, $sort);

        $this->assertEquals($listBranch1, $listBranch1_);


        $kbs->deleteBranch($branch1);
        $this->assertEmpty($kbs->listBranch($branch1));
        $this->assertEquals($listBranch0, $listBranch0_);

        $kbs->deleteBranch($branch0);
        $this->assertEmpty($kbs->listBranch($branch0));
        $this->assertEmpty($kbs->listBranch($branch1));
        $this->assertEmpty($kbs->list());

    }

    // copyBranchTo
    public function test_3() {

        $sort = function ($a, $b) {
            if ($a['key'] != $b['key']) {
                return $a['key'] <=> $b['key'];
            }
            return $a['branch'] <=> $b['branch'];
        };

        $ks = new MemoryKeyStorage();
        $kbs = new KeyBranchStorage($ks);

        $branch0 = 0;

        $key10 = $this->faker->lexify('???????????');
        $val10 = $this->faker->sentence();
        $kbs->set($key10, $branch0, $val10);

        $key20 = $this->faker->lexify('???????????');
        $val20 = $this->faker->sentence();
        $kbs->set($key20, $branch0, $val20);

        $key30 = $this->faker->lexify('???????????');
        $val30 = $this->faker->sentence();
        $kbs->set($key30, $branch0, $val30);


        $branch1 = 0;

        $key11 = $this->faker->lexify('???????????');
        $val11 = $this->faker->sentence();
        $kbs->set($key11, $branch1, $val11);

        $key21 = $this->faker->lexify('???????????');
        $val21 = $this->faker->sentence();
        $kbs->set($key21, $branch1, $val21);

        $key31 = $this->faker->lexify('???????????');
        $val31 = $this->faker->sentence();
        $kbs->set($key31, $branch1, $val31);



        $listBranch0 = [
            ['key' => $key10, 'branch' => $branch0],
            ['key' => $key20, 'branch' =>$branch0],
            ['key' => $key30, 'branch' =>$branch0],
        ];
        $listBranch0 = usort($listBranch0, $sort);

        $listBranch0_ = $kbs->listBranch($branch0);
        $listBranch0_ = usort($listBranch0_, $sort);

        $this->assertEquals($listBranch0, $listBranch0_);


        $listBranch1 = [
            ['key' => $key11, 'branch' => $branch1],
            ['key' => $key21, 'branch' =>$branch1],
            ['key' => $key31, 'branch' =>$branch1],
        ];
        $listBranch1 = usort($listBranch1, $sort);

        $listBranch1_ = $kbs->listBranch($branch1);
        $listBranch1_ = usort($listBranch1_, $sort);

        $this->assertEquals($listBranch1, $listBranch1_);


        $kbs->copyBranchTo($branch1, $branch0);

        $listBranch0_ = $kbs->listBranch($branch0);
        $listBranch0_ = usort($listBranch0_, $sort);

        $this->assertEquals($listBranch1, $listBranch0_);

        $this->assertEquals($listBranch1, $listBranch1_);

    }


}