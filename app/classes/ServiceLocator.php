<?php

declare(strict_types = 1);

namespace App\Classes;

use InvalidArgumentException;
use App\Interfaces\ServiceInterface as Service;


class ServiceLocator
{
    private static self $instance;

    /**
     * @var string[][]
     */
    private array $services = [];

    /**
     * @var Service[]
     */
    private array $instantiated = [];

    public static function instance() : self {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {}
    private function __clone(): void {}
    public function __wakeup(): void {
        throw new \Exception("Cannot unserialize a singleton.");
    }


    public function addInstance(string $name, Service $service)
    {
        $this->instantiated[$name] = $service;
    }

    public function addClass(string $name, string $class, array $params = [])
    {
        array_unshift($params, $class);
        $this->services[$name] = $params;
    }

    public function has(string $name): bool
    {
        return isset($this->services[$name]) || isset($this->instantiated[$name]);
    }

    public function get(string $name): Service
    {
        if (isset($this->instantiated[$name])) {
            return $this->instantiated[$name];
        }

        if (!isset($this->services[$name])) {
            throw new \Exception('Bad service name:'.$name);
        }

        $args = $this->services[$name];
        $class = array_shift($args);
        $object = new $class(...$args);

        if (!$object instanceof Service) {
            throw new InvalidArgumentException('Could not register service: is no instance of Service');
        }

        $this->instantiated[$name] = $object;

        return $object;
    }
}