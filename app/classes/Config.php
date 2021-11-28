<?php

declare(strict_types = 1);

namespace App\Classes;

use App\Classes\DotEnv;

class Config
{
    public bool $is_mining = true;
    public int $mining_award = 10;

    public int $transaction_default_ttl = 3600 * 3;

    public array $default_nodes = [
        '127.0.0.1:8001',
        '127.0.0.1:8002',
        '127.0.0.1:8003',
    ];

    public string $coinname = 'phpcoin_123';
    public string $version = '0.01';

    public string $node_private_key = <<<EOD
-----BEGIN PRIVATE KEY-----
MIIBUwIBADANBgkqhkiG9w0BAQEFAASCAT0wggE5AgEAAkEApKWOrEMfKjlDMGsJ
aJC2kniyGBf9vnJzr7t16N5kYpwccVk/pB2edl3GY8ASxKv0ZMHQL4gz5XIYB/VA
RYONgwIDAQABAkBkN42fVv/aQJ6gExbX+fXXM/Ybakb+LEY0eiNsCioKRserYpr1
sNQKxQToQweWw2j9gV/WWfTa7+BCkDjl69NJAiEAz2vYarqwbQ9YDdJfytPtxFrp
ITgHP5iCaYT05zSkD4UCIQDLNR6GwfxrkRNkbSf83rW0j2RZ+bGDnDGm9kGfpqPD
ZwIgGee1Mrc4O5az/53rmtBXHLPh8+UkepvYhcc2Mv4PE2UCIDwj6HjxiIc9VIPw
WllYgGaD2atXXtYYsAk98IYTh3wZAiAoDglngefcbV1K5bZlNXHjtKkRiCTOitrM
+TafjD0iJQ==
-----END PRIVATE KEY-----
EOD;

    public function __construct() {
        // load env before (class DotEnv)

        $env_filename = __DIR__ . '/../../.env';
        if (!file_exists($env_filename)) {
            return;
        }
        (new DotEnv($env_filename))->load();

        $this->is_mining = bool(self::env('is_mining', $this->is_mining));
        $this->mining_award = int(self::env('mining_award', $this->mining_award));
        $this->transaction_default_ttl = int(self::env('transaction_default_ttl', $this->transaction_default_ttl));
        $this->default_nodes = self::env('default_nodes') ? explode(',', self::env('default_nodes')) : $this->default_nodes;
        $this->coinname = string(self::env('coinname', $this->transaction_default_ttl));
        $this->version = string(self::env('version', $this->transaction_default_ttl));
        $this->node_private_key = string(self::env('node_private_key', $this->transaction_default_ttl));
    }

    public static function env($key, $default = null) : string
    {
        return getenv($key) ?? $default;
    }
}