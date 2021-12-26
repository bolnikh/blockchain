<?php


declare(strict_types=1);

namespace Domain\Actions;

use Domain\Interfaces\RunnableInterface;
use Domain\KeyMaster;


class CreateKeysPair implements RunnableInterface
{
    private KeyMaster $keyMaster;
    private string $privateKey;
    private string $publicKey;

    public function __construct()
    {
        $this->keyMaster = new KeyMaster();
    }


    public function run() : void
    {
        $this->keyMaster->generateKey();

        $this->privateKey = $this->keyMaster->getPrivateKey();
        $this->publicKey = $this->keyMaster->getPublicKey();
    }

    public function getPrivateKey(bool $oneLine = true) : string
    {
        return $oneLine ? $this->keyMaster->makeOneLineKey($this->privateKey) : $this->privateKey;
    }

    public function getPublicKey(bool $oneLine = true) : string
    {
        return $oneLine ? $this->keyMaster->makeOneLineKey($this->publicKey) : $this->publicKey;
    }
}