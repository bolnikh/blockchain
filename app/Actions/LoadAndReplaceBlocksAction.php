<?php

declare(strict_types=1);

namespace App\Actions;


use App\Classes\NodeDataTransfer;
use App\Classes\ServiceLocator;
use Domain\BlockExists;
use Domain\Interfaces\NodeStorageInterface;
use Domain\Interfaces\BlockChainStorageInterface;
use Domain\Storages\BlockChainStorageUnion;

class LoadAndReplaceBlocksAction
{

    private ServiceLocator $sl;
    private NodeStorageInterface $ns;
    private BlockChainStorageInterface $bs;

    public function __construct()
    {
        $this->sl = ServiceLocator::instance();

        $this->ns = $this->sl->get('NodeStorage');

        $this->bs = $this->sl->get('BlockChainStorage');
    }

    /**
     *
     *
     * @todo надо это похоже переписывать
     *
     * 0) если текущее макс ид = 0 - грузим все из чужой ноды,
     * 1) определяем макс ид внешее, если оно меньше чем текущее , то пропускаем ноду
     * 2) далее ищем последнее совпадение по hash для блоков
     * 3) далее смотрим следущий блок за последним совпавшим
     * если у нас такого блока нет - грузим все начиная с этого блока
     * если у нашего блока хеш меньше чем у чужого - не грузим оттуда ничего
     * если у нашего блока хеш больше чем у чужого - грузим все с чужого начиная с этого блока
     */
    public function run() : void
    {
        foreach ($this->ns->getList(false) as $node) {
            $ndt = new NodeDataTransfer($node);

            if ($this->bs->getMaxId() == 0) {
                $this->loadAll($ndt);
                return;
            }

            try {
                $maxIdArr = $ndt->send([
                    'controller' => 'BlockChain',
                    'method' => 'getMaxId',
                    'params' => [
                    ]
                ]);
            } catch (\Exception $e) {
                continue;
            }

            $outerMaxId = $maxIdArr['max_id'];

            $currMaxId = $this->bs->getMaxId();

            if ($currMaxId > $outerMaxId) {
                continue;
            }

            $this->loadPart($ndt, $outerMaxId);


//            if ($outerMaxId - $currMaxId < 20) {
//                try {
//                    $blkJsonArr = $ndt->send([
//                        'controller' => 'BlockChain',
//                        'method' => 'getLastArr',
//                        'params' => [
//                            'num' => 30,
//                        ]
//                    ]);
//                } catch (\Exception $e) {
//                    continue;
//                }
//
//                $blkArr = [];
//                foreach ($blkJsonArr['blkArr'] as $arr) {
//                    $blkArr[] = BlockExists::fromArr($arr);
//                }
//
//                try {
//                    $bcsu = new BlockChainStorageUnion($this->bs, $blkArr);
//                    $bcsu->merge();
//                } catch (BlockChainUnionException $e) {
//                    // @todo тут надо глубже выяснять как можно смержить. стандартных 10 блоково не хватило
//                    // возможно с нуля надо загружать
//                }
//            } else {
//
//            }



        }

    }

    public function loadAll($ndt) : void
    {
        try {
            $maxIdArr = $ndt->send([
                'controller' => 'BlockChain',
                'method' => 'getMaxId',
                'params' => [
                ]
            ]);

            $maxId = $maxIdArr['max_id'];
            if ($maxId == 0) {
                return;
            }

            $offset = 0;
            $limit = 30;
            $this->_subLoadAll($ndt, $maxId, $offset, $limit);

        } catch (\Exception $e) {
            return;
        }
    }

    private function _subLoadAll($ndt, $maxId, $offset, $limit)
    {

        while ($offset < $maxId) {
            $blkJsonArr = $ndt->send([
                'controller' => 'BlockChain',
                'method' => 'getAll',
                'params' => [
                    'offset' => $offset,
                    'limit' => $limit,
                ]
            ]);

            foreach ($blkJsonArr['blkArr'] as $arr) {
                $blk = BlockExists::fromArr($arr);

                $this->bs->store($blk);
            }

            $offset += $limit;
        }
    }

    private function loadPart($ndt, int $outerMaxId) : void
    {
        try {
            $lastEqualHashBlkId = $this->lastEqualHashBlkId($ndt, $outerMaxId);
            if ($lastEqualHashBlkId == 0) {
                $this->loadAll($ndt);
            } else {
                $offset = $lastEqualHashBlkId;
                $limit = 30;
                $this->_subLoadAll($ndt, $outerMaxId, $offset, $limit);
            }
        } catch (\Exception $e) {
            return;
        }
    }


    private function lastEqualHashBlkId($ndt, int $outerMaxId) : int
    {

            $limit = 100;
            $offset = $outerMaxId - 100;

            while ($offset > -$limit) {
                $offset = $offset < 0 ? 0 : $offset;

                $hashArr = $ndt->send([
                    'controller' => 'BlockChain',
                    'method' => 'getHashes',
                    'params' => [
                        'offset' => $offset,
                        'limit' => $limit,
                    ]
                ]);

                $ha = $hashArr['hashArr'];
                krsort($ha); // в обратном порядке с макс ид до мин ид, чтобы найти первое совпадение двигаясь по массиву
                foreach ($ha as $blkId => $hash) {
                    $blk = $this->bs->getById($blkId);
                    if ($blk->getHash() == $hash) {
                        return $blkId;
                    }
                }

                $offset -= $limit;
            }

        return 0;
    }
}