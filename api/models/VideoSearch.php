<?php

namespace app\models;

use Illuminate\Support\Arr;
use yii\data\ActiveDataProvider;
use app\components\ModelHelper;
use yii\db\ActiveQuery;

/**
 * VideoSearch represents the model behind the search form of `app\models\Video`.
 */
class VideoSearch extends Video
{
    const SCENARIO_LIST_USER = 'list-user';
    const SCENARIO_LIST_STAFF = 'list-staff';

    public static function tableName()
    {
        return 'videos';
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Video::find();

        // Filtering
        $query->andFilterWhere(['id' => $this->id]);

        $query->andFilterWhere(['like', 'title', Arr::get($params, 'title')]);

        $query->andFilterWhere(['=', 'category_id', Arr::get($params, 'category_id')]);

        $query->andFilterWhere(['<>', 'status', Video::STATUS_DELETED]);

        if ($this->scenario === self::SCENARIO_LIST_USER) {
            return $this->getQueryListUser($query, $params);
        } else {
            return $this->getQueryListStaff($query, $params);
        }
    }

    protected function getQueryListUser($query, $params)
    {
        $filterStatusList = [
            Video::STATUS_ACTIVE,
        ];

        $query->andFilterWhere(['in', 'status', $filterStatusList]);

        $query->andFilterWhere(['=', 'kabkota_id', Arr::get($params, 'kabkota_id')]);

        return $this->createActiveDataProvider($query, $params);
    }

    protected function getQueryListStaff($query, $params)
    {
        $query = $this->filterByStaffArea($query, $params);

        return $this->createActiveDataProvider($query, $params);
    }

    protected function createActiveDataProvider($query, $params)
    {
        $pageLimit = Arr::get($params, 'limit');
        $sortBy    = Arr::get($params, 'sort_by', 'created_at');
        $sortOrder = Arr::get($params, 'sort_order', 'descending');
        $sortOrder = ModelHelper::getSortOrder($sortOrder);

        return new ActiveDataProvider([
            'query'      => $query,
            'sort'       => [
                'defaultOrder' => [$sortBy => $sortOrder],
                'attributes' => [
                    'title',
                    'category_id',
                    'created_at',
                    'total_likes',
                    'seq',
                    'source',
                    'status',
                ],
            ],
            'pagination' => [
                'pageSize' => $pageLimit,
            ],
        ]);
    }

    protected function filterByStaffArea(ActiveQuery $query, $params)
    {
        if (Arr::has($params, 'kabkota_id')) {
            $query->andWhere(['or',
                ['kabkota_id' => $params['kabkota_id']],
                ['kabkota_id' => null]]);
        }

        return $query;
    }
}
