<?php

namespace Tests\Unit;

require_once __DIR__ . '\..\..\app\domain\KeyMaster.php';
require_once __DIR__ . '\..\..\app\domain\Sign.php';
require_once __DIR__ . '\..\..\app\domain\Hash.php';
require_once __DIR__ . '\..\..\app\domain\Exceptions\KeyMasterException.php';

require_once __DIR__ . '\..\..\app\domain\BlockExists.php';
require_once __DIR__ . '\..\..\app\domain\BlockNew.php';
require_once __DIR__ . '\..\..\app\domain\BlockChain.php';

use PHPUnit\Framework\TestCase;
use Domain\KeyMaster;
use Domain\Sign;
use Domain\KeyMasterException;
use Domain\Hash;
use Domain\BlockExists;
use Domain\BlockNew;
use Domain\BlockChain;


class BlockChainTest  extends TestCase
{
    public function create_new_block_test()
    {

    }


}