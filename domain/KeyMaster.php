<?php

declare(strict_types = 1);

namespace Domain;

use Domain\Exceptions\KeyMasterException;

class KeyMaster
{
    private \OpenSSLAsymmetricKey|false $key;

    private array $opensslConfig = array(
        "digest_alg" => "sha256",
        "private_key_bits" => 512,
        "private_key_type" => OPENSSL_KEYTYPE_RSA,
    );

    public function __construct(
        private string $privateKey = ''
    )
    {
        if (!empty($privateKey)) {
            $this->loadKey();
        }
    }

    private function loadKey()
    {
        $this->key = openssl_pkey_get_private($this->privateKey);
    }

    public function generateKey() : void
    {
        $this->key = openssl_pkey_new($this->opensslConfig);
    }

    public function getSign(string $text) : string
    {
        $res = openssl_sign($text, $signature, $this->key);
        if ($res === false) {
            throw new KeyMasterException('can not sign text');
        }
        return base64_encode($signature);
    }

    public function getPublicKey(bool $oneLine = false)
    {
        $publickey = openssl_pkey_get_details($this->key);
        if ($publickey === false) {
            throw new KeyMasterException('can not get public key');
        }
        $publickey = $publickey["key"];

        return $oneLine ? $this->makeOneLineKey($publickey) : $publickey;
    }

    public function getPrivateKey(bool $oneLine = false)
    {
        $res = openssl_pkey_export($this->key, $privatekey);
        if ($res === false) {
            throw new KeyMasterException('can not get private key');
        }

        return $oneLine ? $this->makeOneLineKey($privatekey) : $privatekey;
    }


    public function makeOneLineKey($key) : string
    {
        $key = str_replace('-----BEGIN PUBLIC KEY-----','', $key);
        $key = str_replace('-----END PUBLIC KEY-----','', $key);
        $key = str_replace('-----BEGIN PRIVATE KEY-----','', $key);
        $key = str_replace('-----END PRIVATE KEY-----','', $key);

        $key = str_replace(["\r", "\n"], '', $key);

        return $key;
    }
}