<?php


declare(strict_types=1);

namespace App\Controller\Api;

use App\Classes\Config;

class InfoController
{

    public function action_index($copy_get = []) : array
    {
        $config = new Config();

        $arr = [
            'coinname' => $config->coinname,
            'version' => $config->version,
            'maxBlockId' => 10,
        ];

        return $arr;
    }


    public function action_max_block_id($copy_get = []) : array
    {
        return [
            'maxBlockId' => 10,
        ];
    }


    public function action_free_transactions($copy_get = []) : array
    {
        return [];
    }

    public function action_hosts($copy_get = []) : array
    {
        return [];
    }
}