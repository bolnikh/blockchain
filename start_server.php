<?php

chdir(__DIR__);

if (empty($argv[1])) {
    die('Add server credentials, for example 127.0.0.1:800 ');
}
exec('php -S '.$argv[1].' -t web/');
