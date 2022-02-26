<?php


declare(strict_types=1);

namespace App\Classes;

use Domain\Node;

class NodeDataTransfer
{
    private Config $config;

    public function __construct(private Node $node)
    {
        $sl = ServiceLocator::instance();
        $this->config = $sl->get('Config');
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
            $data['controller'] = 'index';
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

//        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

//        if ($status != 200) {
//            die("Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
//        }


        curl_close($curl);
//var_dump($status);
//var_dump($json_response);
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