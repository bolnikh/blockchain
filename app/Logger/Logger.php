<?php

declare(strict_types=1);

namespace App\Logger;

use App\Interfaces\ServiceInterface;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use SplObjectStorage;

/**
 * Class Logger
 */
class Logger extends AbstractLogger implements LoggerInterface, ServiceInterface
{
    /**
     * @var SplObjectStorage Список роутов
     */
    public $routes;

    /**
     * Конструктор
     */
    public function __construct()
    {
        $this->routes = new SplObjectStorage();
    }

    /**
     * @inheritdoc
     */
    public function log($level, $message, array $context = [])
    {
        foreach ($this->routes as $route)
        {
            if (!$route instanceof Route)
            {
                continue;
            }
            if (!$route->isEnable)
            {
                continue;
            }
            $route->log($level, $message, $context);
        }
    }
}