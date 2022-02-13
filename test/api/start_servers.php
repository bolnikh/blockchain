<?php

chdir(__DIR__.'/../../web/');
$servers = file(__DIR__.'/test_servers.txt');

foreach ($servers as $server) {
    start_server($server);
}


function start_server(string $server) : void
{
    exec('php -S '.$server);
}