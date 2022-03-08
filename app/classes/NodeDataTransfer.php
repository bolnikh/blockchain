<?php


declare(strict_types=1);

namespace App\Classes;

use _PHPStan_9488d3497\Psr\Log\LogLevel;
use Domain\Node;
use App\Logger\Logger;

class NodeDataTransfer
{
    private ServiceLocator $sl;
    private Config $config;
    private Logger $logger;

    public function __construct(private Node $node)
    {
        $this->sl = ServiceLocator::instance();
        $this->config = $this->sl->get('Config');
        $this->logger = $this->sl->get('Logger');

    }

    public function send(array $data) : array|bool
    {
        if (empty($data['coinname'])) {
            $data['coinname'] = $this->config->coinname;
        }
        if (empty($data['version'])) {
            $data['version'] = $this->config->version;
        }

        if (empty($data['controller'])) {
            $data['controller'] = 'info';
        }
        if (empty($data['method'])) {
            $data['method'] = 'index';
        }
        if (!isset($data['params'])) {
            $data['params'] = [];
        }

        $post_fields = json_encode($data);

        $curl = curl_init($this->node->url());
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER,
            array("Content-type: application/json"));
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_fields);

        $json_response = curl_exec($curl);

        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        $data_json = json_encode($data);
        $log_str = "
            SEND
            $data_json
            STATUS
            $status
            RESPONSE
            $json_response
        ";
        $this->logger->debug($log_str);

        curl_close($curl);

        return is_string($json_response) ? json_decode($json_response, true) : false;
    }

    public function isActive() : bool
    {
        try {
            $data = $this->send([
                'controller' => 'info',
                'method' => 'index',
            ]);
        } catch (\Exception $e) {
            return false;
        }

        return is_array($data)
            && $data['coinname'] === $this->config->coinname
            && $data['version'] === $this->config->version
            ;
    }

    public function ping() : void
    {
        if ($this->isActive()) {
            $this->node->setActive(true);
            $this->node->setLastActiveAt(time());
        } else {
            $this->node->setActive(false);
        }

        $sl = ServiceLocator::instance();
        $nodeStorage = $sl->get('NodeStorage');
        $nodeStorage->store($this->node);
    }

    public function getNewTrx()
    {
        try {
            $data = $this->send([
                'controller' => 'transaction',
                'method' => 'index',
            ]);
        } catch (\Exception $e) {
            return false;
        }
    }
}