<?php


declare(strict_types=1);

namespace Domain\Storages;


use App\Interfaces\ServiceInterface;
use Domain\BlockExists;
use Domain\BlockNew;
use Domain\TransactionExists;


class BlockChainStorageFile  extends BlockChainStorageAbstract implements ServiceInterface
{

    private $storageDir = __DIR__.'/../../storage/files/';

    public function __construct() {
        parent::__construct();

    }


    public function store(BlockNew|BlockExists $block) : void
    {
        $json_obj = json_encode($block);
        file_put_contents($this->blockFileName($block), $json_obj);

    }

    private function blockFileName(BlockNew|BlockExists|int $block) : string
    {
        if (is_int($block))
        {
            return $this->storageDir.'blocks/'.$block.'.json';
        }
        else
        {
            return $this->storageDir.'blocks/'.$block->id.'.json';
        }
    }

    public function getById(int $id) : BlockNew|BlockExists|null
    {
        assert($id >= 0);

        $blockFileName = $this->blockFileName($id);

        if (file_exists($blockFileName))
        {
            $json_data = file_get_contents($blockFileName);
            $bn_arr = json_decode($json_data, true, 10, JSON_THROW_ON_ERROR);

            $trans = [];
            foreach ($bn_arr['transactions'] as $tr)
            {
                $trans[] = new TransactionExists($tr);
            }

            $bn_arr['transactions'] = $trans;

            return new BlockExists($bn_arr);
        }
        return null;
    }





    public function getMaxId() : int
    {
        $files = scandir($this->storageDir.'blocks/');
        $max_id = 0;

        foreach ($files as $file)
        {
            $file_id = intval($file);
            if ($file_id > $max_id)
            {
                $max_id = $file_id;
            }
        }
        return $max_id;
    }






}