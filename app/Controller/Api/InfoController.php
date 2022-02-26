<?php


declare(strict_types=1);

namespace App\Controller\Api;

use App\Classes\ServiceLocator;

class InfoController
{

    public function action_index() : array
    {
        $sl = ServiceLocator::instance();
        $config = $sl->get('Config');

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