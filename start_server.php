<?php

chdir(__DIR__);

exec('php -S 127.0.0.1:8000 -t web/ route_env.php');
