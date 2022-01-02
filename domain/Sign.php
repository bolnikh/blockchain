<?php

declare(strict_types = 1);

namespace Domain;

use Domain\Exceptions\KeyMasterException;

/**
 * Class Sign
 * проверка подписи текста по сигнатуре и публичному ключу
 * @package Domain
 */
class Sign
{
    public static function check(string $text, $signature, $publicKey) : bool
    {
        if (strpos($publicKey, '-----BEGIN PUBLIC KEY-----') === false) {
            $publicKey = <<<EOD
-----BEGIN PUBLIC KEY-----
$publicKey
-----END PUBLIC KEY-----
EOD;
        }

        $res = openssl_verify($text, base64_decode($signature), $publicKey);
        if ($res === false || $res === -1) {
            throw new KeyMasterException('can not verify text');
        }
        return $res === 1;
    }
}