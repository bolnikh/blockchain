<?php

$service = '';
require_once __DIR__.'/../app/bootstrap.php';


$a = new \Domain\Actions\CreateKeysPair();
$a->run();

echo <<<EOD
----------

{$a->getPublicKey()}

---------

{$a->getPrivateKey()}

--------
EOD;
