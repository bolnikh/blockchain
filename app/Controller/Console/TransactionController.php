<?php


declare(strict_types=1);

namespace App\Controller\Console;

class TransactionController
{

    public function action_send_trxs(array $trxs) : array
    {
        $data = [];
        foreach ($trxs as $trx) {
            $data[] = json_encode($trx);
        }

        return $data;
    }



}