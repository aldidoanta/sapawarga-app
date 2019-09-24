<?php

namespace app\models;

use Illuminate\Support\Arr;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CategorySearch represents the model behind the search form of `app\models\Category`.
 */
class CategorySearch extends Category
{
    const SCENARIO_LIST_ADMIN = 'list-admin';
    const SCENARIO_LIST_SABERHOAX = 'list-saberhoax';
    const SCENARIO_LIST_OTHER_ROLE = 'list-other-role';

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name', 'type'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        $scenarios = Model::scenarios();
        $attributes = ['id', 'name', 'type'];

        $scenarios[self::SCENARIO_LIST_ADMIN] = $attributes;
        $scenarios[self::SCENARIO_LIST_SABERHOAX] = $attributes;
        $scenarios[self::SCENARIO_LIST_OTHER_ROLE] = $attributes;
        return $scenarios;
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
        $query = Category::find();

        // add conditions that should always apply here

        $sortBy    = Arr::get($params, 'sort_by', 'name');
        $sortOrder = Arr::get($params, 'sort_order', 'ascending');
        $sortOrder = $this->getSortOrder($sortOrder);

        $pageLimit = Arr::get($params, 'limit');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => [$sortBy => $sortOrder]],
            'pagination' => [
                'pageSize' => $pageLimit,
            ],
        ]);

        if (isset($params['all']) && $params['all'] == true) {
            $dataProvider->setPagination(false);
        }

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'name', Arr::get($params, 'name')]);
        $query->andFilterWhere(['like', 'type', Arr::get($params, 'type')]);

        if ($this->scenario === self::SCENARIO_LIST_SABERHOAX) {
            // Hanya menampilkan tipe kategori 'newsHoax'
            $query->andFilterWhere(['type' => NewsHoax::CATEGORY_TYPE]);
        } elseif ($this->scenario === self::SCENARIO_LIST_ADMIN) {
            // Tidak menampilkan tipe kategori 'notification'
            $query->andFilterWhere(['<>', 'type', Notification::CATEGORY_TYPE]);
        } elseif ($this->scenario === self::SCENARIO_LIST_OTHER_ROLE) {
            // Tidak menampilkan tipe kategori 'newsHoax' dan 'notification'
            $query->andFilterWhere(['not in', 'type', Category::EXCLUDED_TYPES]);
        }

        return $dataProvider;
    }

    protected function getSortOrder($sortOrder)
    {
        switch ($sortOrder) {
            case 'descending':
                return SORT_DESC;
                break;
            case 'ascending':
            default:
                return SORT_ASC;
                break;
        }
    }
}
