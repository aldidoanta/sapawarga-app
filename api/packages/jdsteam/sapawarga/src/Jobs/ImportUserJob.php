<?php

namespace Jdsteam\Sapawarga\Jobs;

use yii\base\BaseObject;
use yii\queue\JobInterface;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class ImportUserJob extends BaseObject implements JobInterface
{
    public $file;

    public function execute($queue)
    {
        $reader = ReaderEntityFactory::createCSVReader();
        $reader->open($this->file);

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $cells = $row->getCells();

                var_dump($cells); exit;
            }
        }

        $reader->close();
    }
}
