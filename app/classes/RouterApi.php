<?php

declare(strict_types=1);

namespace App\Classes;

use App\Exceptions\RouteException;
use App\Logger\Logger;

class RouterApi
{
    private string $prefix = 'Api';
    private ServiceLocator $service;
    private Config $config;
    private Logger $logger;

    public function __construct()
    {
        $this->service = ServiceLocator::instance();
        $this->config = $this->service->get('Config');
        $this->logger = $this->service->get('Logger');
    }

    public function run()
    {
        try {
            $inputJSON = file_get_contents('php://input');
            if (empty($inputJSON)) {
                $data = [
                    'coinname' => $this->config->coinname,
                    'version' => $this->config->version,
                    'controller' => 'info',
                    'method' => 'index',
                    'params' => [],
                ];
            } else {
                $data = json_decode($inputJSON, true, 10, JSON_THROW_ON_ERROR);

                if ($data['coinname'] != $this->config->coinname) {
                    throw new RouteException('Bad coinname');
                }

                if ($data['version'] != $this->config->version) {
                    throw new RouteException('Bad version - please upgrade your code');
                }
            }

            if (!isset($data['controller'])) {
                throw new RouteException('Bad controller');
            }

            if (!isset($data['method'])) {
                throw new RouteException('Bad method');
            }

            if (!isset($data['params'])) {
                $data['params'] = [];
            }

            $controller = 'App\\Controller\\' . $this->prefix . '\\' . ucfirst($data['controller']) . 'Controller';
            $method = 'action_' . $data['method'];

            if (!method_exists($controller, $method)) {
                throw new RouteException('Controller or method not exists');
            }

            $contObj = new $controller();
            $result = $contObj->$method($data['params']);

            header('Content-type: application/json');
            echo $result_json = json_encode($result, JSON_UNESCAPED_UNICODE);

            $url = $_SERVER['REMOTE_ADDR'];
            $data_json = json_encode($data);
            $log_str = "
            GET $url
            $data_json
            RESPONSE
            $result_json
        ";
            $this->logger->debug($log_str);

        } catch (\Exception $e) {
            header('Content-type: application/json');
            echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }
}
