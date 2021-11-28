<?php

namespace Tests\Unit;

require_once __DIR__ . '\..\..\vendor\autoload.php';

use PHPUnit\Framework\TestCase;
use Domain\Difficulty;




class DifficultyTest extends TestCase
{
    public function test_difficulty()
    {
        $d1 = new Difficulty('00f');

        $this->assertTrue($d1->check('00f'));
        $this->assertTrue($d1->check('00e81597216'));
        $this->assertFalse($d1->check('0e81597216'));
    }

}