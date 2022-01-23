<?php

declare(strict_types = 1);

namespace App\Classes;

use App\Interfaces\ServiceInterface;

class Config implements ServiceInterface
{
    public bool $is_mining = true;
    public int $mining_award = 1000;
    public string $difficulty = '000f';

    public int $transaction_default_ttl = 3600 * 3;

    /**
     * @var string[]
     */
    public array $default_nodes = [
        '127.0.0.1:8001',
        '127.0.0.1:8002',
        '127.0.0.1:8003',
    ];

    public string $coinname = 'phpcoin';
    public string $version = '0.01';

    public string $node_private_key = '';
    public string $node_private_key_file = __DIR__.'/../../storage/keys/default_node_private.key';


    public $storage = 'file'; // memory | file | db

    public function __construct() {
        // load env before (class DotEnv)

        $path_to_env =  __DIR__ . '/../../';
        $env_filename = $path_to_env . '.env';
        if (!file_exists($env_filename)) {
            return;
        }
        (new DotEnv($env_filename))->load();

        $this->is_mining = boolval(self::env('is_mining', $this->is_mining));
        $this->mining_award = intval(self::env('mining_award', $this->mining_award));
        $this->difficulty = strval(self::env('difficulty', $this->difficulty));
        $this->transaction_default_ttl = intval(self::env('transaction_default_ttl', $this->transaction_default_ttl));
        $this->default_nodes = self::env('default_nodes') ? explode(',', self::env('default_nodes')) : $this->default_nodes;
        $this->coinname = strval(self::env('coinname', $this->coinname));
        $this->version = strval(self::env('version', $this->version));
        $this->node_private_key_file = strval(self::env('node_private_key_file', $this->node_private_key_file));
        if (strpos($this->node_private_key_file, '__PATH_TO_ENV__') !== false)
        {
            $this->node_private_key_file = str_replace('__PATH_TO_ENV__', $path_to_env, $this->node_private_key_file);
        }
        $this->node_private_key = file_get_contents($this->node_private_key_file);
        $this->storage = strval(self::env('storage', $this->storage));
    }

    public static function env(string $key, string|bool|int|null $default = null) : string|bool|int|null
    {
        return getenv($key) !== false ? getenv($key) : $default;
    }
}