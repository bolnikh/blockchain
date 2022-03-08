<?php


declare(strict_types=1);

namespace App\Controller\Api;


use App\Classes\ServiceLocator;
use Domain\Interfaces\NodeStorageInterface;

class NodeController
{
    public function action_index($params)
    {
        return $this->action_list($params);
    }

    public function action_list($params)
    {
        $this->service = ServiceLocator::instance();
        /**
         * @var NodeStorageInterface
         */
        $nodeStorage = $this->service->get('NodeStorage');

        return $nodeStorage->getList(boolval($params['active'] ?? true));
    }
}