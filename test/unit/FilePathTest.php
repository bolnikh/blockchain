<?php

namespace Tests\Unit;


use Domain\Storages\FileKeyStorage;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '\..\..\vendor\autoload.php';


class FilePathTest extends TestCase
{

    public function test_file_path()
    {
        $fks = new FileKeyStorage($dir = __DIR__.'/../../storage', '', 'json');
        $this->assertEquals($fks->getSubPath('123456'), '123456');


        $fks = new FileKeyStorage($dir = __DIR__.'/../../storage', '1', 'json');
        $this->assertEquals($fks->getSubPath('123456'), '1/23456');

        $fks = new FileKeyStorage($dir = __DIR__.'/../../storage', '2', 'json');
        $this->assertEquals($fks->getSubPath('123456'), '12/3456');

        $fks = new FileKeyStorage($dir = __DIR__.'/../../storage', '2:1', 'json');
        $this->assertEquals($fks->getSubPath('123456'), '12/3/456');

        $fks = new FileKeyStorage($dir = __DIR__.'/../../storage', '2:2', 'json');
        $this->assertEquals($fks->getSubPath('123456'), '12/34/56');


        $fks = new FileKeyStorage($dir = __DIR__.'/../../storage', '2:2', $ext = 'json');
        $this->assertEquals($fks->getFilePath('123456'), $dir.'/'.'12/34/56'.'.'.$ext);
    }

    public function test_smallfile_path()
    {
        $fks = new FileKeyStorage($dir = __DIR__.'/../../storage', '', 'json');
        $this->assertEquals($fks->getSubPath('6'), '6');

        $fks = new FileKeyStorage($dir = __DIR__.'/../../storage', '1', 'json');
        $this->assertEquals($fks->getSubPath('6'), '0/06');

        $fks = new FileKeyStorage($dir = __DIR__.'/../../storage', '2', 'json');
        $this->assertEquals($fks->getSubPath('6'), '00/06');

        $fks = new FileKeyStorage($dir = __DIR__.'/../../storage', '2:1', 'json');
        $this->assertEquals($fks->getSubPath('6'), '00/0/06');

        $fks = new FileKeyStorage($dir = __DIR__.'/../../storage', '2:2', 'json');
        $this->assertEquals($fks->getSubPath('6'), '00/00/06');


        $fks = new FileKeyStorage($dir = __DIR__.'/../../storage', '2:2', $ext = 'json');
        $this->assertEquals($fks->getFilePath('6'), $dir.'/'.'00/00/06'.'.'.$ext);
    }
}