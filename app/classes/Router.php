<?php

declare(strict_types = 1);

namespace App\Classes;

class Router
{
    private string $prefix = 'Api';

    public function __construct()
    {
        $this->prefix = ucfirst($this->prefix);
    }

    public function run()
    {

        if (!in_array($this->prefix, ['Api', 'Web']))
        {
            throw new \Exception('Bad prefix');
        }

        $req = $_GET['req'];
        if (empty($req))
        {
            $req = 'index/index';
        }

        $arr = explode('/', $req);
        if (sizeof($arr) != 2)
        {
            throw new \Exception('Bad req');
        }

        $controller = $arr[0];
        $method = $arr[1];

        if (empty($controller) || empty($method))
        {
            throw new \Exception('Bad req');
        }

        $controller = 'App\\Controller\\'.$this->prefix.'\\'.ucfirst($controller).'Controller';
        $method = 'action_'.$method;

        if (!method_exists($controller, $method))
        {
            throw new \Exception('Controller or method not exists');
        }

        $copy_get = $_GET;
        unset($copy_get['req']);

        $contObj = new $controller();
        $result = $contObj->$method($copy_get);

        if ($this->prefix == 'Api')
        {
            header('Content-type: application/json');
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
        }
        else
        {
            header('Content-type text/html charset=utf-8');
            echo $result;
        }
    }
}