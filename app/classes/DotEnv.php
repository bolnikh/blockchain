<?php

declare(strict_types=1);

namespace App\Classes;

/**
 * Class DotEnv
 * from https://dev.to/fadymr/php-create-your-own-php-dotenv-3k2i
 * @package Domain
 *
 * usage
 * (new DotEnv(__DIR__ . '/.env'))->load();
 * echo getenv('APP_ENV');
 *
 * .env file example
 * APP_ENV=dev
 * # comment - don not use quotes and value must be one line
 * DATABASE_DNS=mysql:host=localhost;dbname=test;
 * DATABASE_USER=root
 *
 */
class DotEnv
{
    /**
     * The directory where the .env file can be located.
     *
     * @var string
     */
    protected string $path;


    public function __construct(string $path)
    {
        if (!file_exists($path)) {
            throw new \InvalidArgumentException(sprintf('%s does not exist', $path));
        }
        $this->path = $path;
    }

    public function load(): void
    {
        if (!is_readable($this->path)) {
            throw new \RuntimeException(sprintf('%s file is not readable', $this->path));
        }

        $lines = file($this->path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#')) {
                continue;
            }

            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                putenv(sprintf('%s=%s', $name, $value));
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}
