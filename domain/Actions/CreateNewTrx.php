<?php

declare(strict_types=1);

namespace Domain\Actions;


use App\Classes\ServiceLocator;
use Domain\Interfaces\RunnableInterface;
use Domain\KeyMaster;
use Domain\TransactionNew;

class CreateNewTrx implements RunnableInterface
{
    private $storage;
    private $config;

    public function __construct(
        private ServiceLocator $service
    )
    {
        $this->storage = $this->service->get('storage');
        $this->config = $this->service->get('config');
    }

    public function run() : void
    {
        $km_from = new KeyMaster($this->config->node_private_key);

        $key_file = ROOT_DIR.'/storage/keys/key'.mt_rand(1,5).'.pem';
        $km_to = new KeyMaster(file_get_contents($key_file));

        $balance_from = $this->storage->balance($km_from->getPublicKey(true));

        if ($balance_from >= 10)
        {
            $tnx = new TransactionNew([
                'private_key' => $this->config->node_private_key,
                'to' => $km_to->getPublicKey(true),
                'amount' => mt_rand(1, 10),
            ]);

            // store to tnx store
        }
    }
}