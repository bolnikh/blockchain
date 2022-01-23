<?php


declare(strict_types=1);

namespace Domain\Storages;


use Domain\Exceptions\TransactionNotExists;
use Domain\TransactionExists;
use Domain\TransactionNew;

class TransactionStorageFile extends TransactionStorageAbstract
{

    private $storageDir = __DIR__.'/../../storage/files/';
    private $storageSubDir = 'transactions/';
    private $fileExt = 'json';

    public function __construct(string $storageDir = '')
    {
        if ($storageDir) {
            $this->storageDir = $storageDir;
        }
    }


    public function trxFileName(TransactionExists|TransactionNew|string $trx) : string
    {
        if (is_string($trx))
        {
            return $this->storageDir.$this->storageSubDir.$trx.'.'.$this->fileExt;
        } else {
            return $this->storageDir.$this->storageSubDir.$trx->hash.'.'.$this->fileExt;
        }
    }

    public function store(TransactionExists|TransactionNew $trx): void
    {
        $json_obj = json_encode($trx);
        file_put_contents($this->trxFileName($trx), $json_obj);
    }

    public function getKeyList(): array
    {
        $files = scandir($this->storageDir.$this->storageSubDir);
        $keys = [];

        foreach ($files as $file)
        {
            $path_parts = pathinfo($file);
            if ($path_parts['extension'] == $this->fileExt)
            {
                $keys[] = $path_parts['filename'];
            }
        }

        sort($keys);
        return $keys;
    }

    public function isExists(string $key): bool
    {
        return file_exists($this->trxFileName($key));
    }

    public function get(string $key): TransactionExists|TransactionNew
    {
        if (!$this->isExists($key))
        {
            throw new TransactionNotExists('Can not get trx');
        }

        $json_data = file_get_contents($this->trxFileName($key));
        $arr = json_decode($json_data, true, 10, JSON_THROW_ON_ERROR);

        return new TransactionExists($arr);
    }

    public function delete(string $key): void
    {
        unlink($this->trxFileName($key));
    }
}