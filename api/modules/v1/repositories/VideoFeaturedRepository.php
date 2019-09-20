<?php

namespace app\modules\v1\repositories;

use app\models\Video;
use app\models\VideoFeatured;
use Illuminate\Support\Arr;
use yii\db\Query;

class VideoFeaturedRepository
{
    public function getList($params = [])
    {
        $limit = 5;

        /**
         * @var Query $query
         */
        $query   = VideoFeatured::find();
        $query   = $this->getListBuildFilterQuery($query, $params);
        $records = $query->with('video')->limit($limit)->all();

        $rows = [];

        for ($n = 1; $n <= $limit; $n++) {
            $rows[] = $this->getListMatchSequence($records, $n);
        }

        return $rows;
    }

    protected function getListBuildFilterQuery(Query $query, $params = []): Query
    {
        $query->leftJoin('videos', '`video_featured`.`video_id` = `videos`.`id`');
        $query->andWhere(['videos.status' => Video::STATUS_ACTIVE]);

        $kabkotaId = Arr::get($params, 'kabkota_id');

        if ($kabkotaId !== null) {
            return $query->andWhere(['video_featured.kabkota_id' => $kabkotaId]);
        }

        return $query->andWhere(['video_featured.kabkota_id' => null]);
    }

    protected function getListMatchSequence($records, $n)
    {
        $record = Arr::first($records, function ($record) use ($n) {
            return $record->seq === $n;
        });

        if ($record !== null) {
            return $record;
        }

        return null;
    }

    public function resetFeatured($kabkotaId)
    {
        if ($kabkotaId === null) {
            return VideoFeatured::deleteAll('kabkota_id is null');
        }

        return VideoFeatured::deleteAll('kabkota_id = :kabkota_id', ['kabkota_id' => $kabkotaId]);
    }
}
