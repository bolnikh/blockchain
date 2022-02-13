<?php


declare(strict_types=1);

namespace App\Controller\Api;

use App\Classes\Config;

class InfoController
{

    public function action_index() : array
    {
        $config = new Config();

        $arr = [
            'coinname' => $config->coinname,
            'version' => $config->version,
        ];

        return $arr;
    }






    public function action_hosts() : array
    {
        return [];
    }
}