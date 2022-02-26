<?php

declare(strict_types = 1);

namespace Domain;


class Node
{
    private string $ip;
    private string $port;
    private bool $active = false;
    private int $last_active_at = 0;


    private array $fillable = [
        'ip', 'port', 'active', 'last_active_at',
    ];


    public function __construct(array $data = [])
    {
        foreach ($data as $k => $v) {
            if (in_array($k, $this->fillable)) {
                $this->$k = $v;
            }
        }
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     */
    public function setIp(string $ip): void
    {
        $this->ip = $ip;
    }

    /**
     * @return int
     */
    public function getPort(): string
    {
        return $this->port;
    }

    /**
     * @param int $port
     */
    public function setPort(string $port): void
    {
        $this->port = $port;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    /**
     * @return int
     */
    public function getLastActiveAt(): int
    {
        return $this->last_active_at;
    }

    /**
     * @param int $last_active_at
     */
    public function setLastActiveAt(int $last_active_at): void
    {
        $this->last_active_at = $last_active_at;
    }


    public function getHash() :string
    {
        return $this->ip.'_'.$this->port;
    }


    public function toArray() : array
    {
        return [
            'ip' => $this->ip,
            'port' => $this->port,
            'active' => $this->active,
            'last_active_at' => $this->last_active_at,
        ];
    }

    public function toJson() : string
    {
        return json_encode($this->toArray());
    }

    public function url() : string
    {
        return 'http://'.$this->ip.':'.$this->port.'/api.php';
    }
}