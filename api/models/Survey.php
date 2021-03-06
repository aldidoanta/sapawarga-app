<?php

namespace app\models;

use app\components\ModelHelper;
use app\validator\InputCleanValidator;
use Jdsteam\Sapawarga\Behaviors\AreaBehavior;
use Jdsteam\Sapawarga\Models\Concerns\HasArea;
use Jdsteam\Sapawarga\Models\Concerns\HasCategory;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "survey".
 *
 * @property int $id
 * @property int $category_id
 * @property string $title
 * @property string $external_url
 * @property string $response_url
 * @property int $kabkota_id
 * @property int $kec_id
 * @property int $kel_id
 * @property string $rw
 * @property mixed $meta
 * @property int $status
 */
class Survey extends ActiveRecord
{
    use HasArea, HasCategory;

    const STATUS_DELETED = -1;
    const STATUS_DRAFT = 0;
    const STATUS_DISABLED = 1;
    const STATUS_PUBLISHED = 10;
    const STATUS_STARTED = 15;
    const STATUS_ENDED = 20;

    const CATEGORY_TYPE = 'survey';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'survey';
    }

    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['title', 'status', 'external_url', 'category_id'], 'required'],
            [['title', 'status', 'external_url', 'response_url', 'category_id'], 'trim'],
            ['title', 'string', 'min' => 10],
            ['title', 'string', 'max' => 100],
            ['title', InputCleanValidator::class],
            [['external_url', 'response_url'], 'url'],
            [['kabkota_id', 'kec_id', 'kel_id'], 'integer'],
            [['start_date', 'end_date'], 'date', 'format' => 'php:Y-m-d'],
            ['start_date', 'compare', 'compareAttribute' => 'end_date', 'operator' => '<'],
            ['end_date', 'compare', 'compareAttribute' => 'start_date', 'operator' => '>'],
            ['status', 'in', 'range' => [-1, 0, 1, 10]],
        ];

        return array_merge(
            $rules,
            $this->rulesRw(),
            $this->rulesCategory()
        );
    }

    public function fields()
    {
        return [
            'id',
            'category_id',
            'category' => 'CategoryField',
            'title',
            'external_url',
            'response_url',
            'start_date',
            'end_date',
            'meta',
            'status',
            'status_label' => 'StatusLabel',
            'kabkota_id',
            'kabkota' => 'KabkotaField',
            'kec_id',
            'kecamatan' => 'KecamatanField',
            'kel_id',
            'kelurahan' => 'KelurahanField',
            'rw',
            'created_at',
            'updated_at',
        ];
    }

    protected function getStatusLabel()
    {
        $statusLabel = '';

        switch ($this->status) {
            case self::STATUS_PUBLISHED:
                $statusLabel = Yii::t('app', 'status.published');
                break;
            case self::STATUS_DISABLED:
                $statusLabel = Yii::t('app', 'status.inactive');
                break;
            case self::STATUS_DRAFT:
                $statusLabel = Yii::t('app', 'status.draft');
                break;
            case self::STATUS_DELETED:
                $statusLabel = Yii::t('app', 'status.deleted');
                break;
        }

        return $statusLabel;
    }

    public function afterSave($insert, $changedAttributes)
    {
        $isSendNotification = ModelHelper::isSendNotification($insert, $changedAttributes, $this);

        if ($isSendNotification) {
            $categoryName = Notification::CATEGORY_LABEL_SURVEY;
            $payload = [
                'categoryName'  => $categoryName,
                'title'         => "{$categoryName}: {$this->title}",
                'description'   => null,
                'target'        => [
                    'kabkota_id'    => $this->kabkota_id,
                    'kec_id'        => $this->kec_id,
                    'kel_id'        => $this->kel_id,
                    'rw'            => $this->rw,
                ],
                'meta'          => [
                    'target'    => 'survey',
                    'url'       => $this->external_url,
                ],
            ];

            ModelHelper::sendNewContentNotification($payload);
        }

        return parent::afterSave($insert, $changedAttributes);
    }

    /** @inheritdoc */
    public function behaviors()
    {
        return [
            [
                'class'              => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value'              => time(),
            ],
            AreaBehavior::class,
        ];
    }

    /**
     * Checks if category_id is current user's id
     *
     * @param $attribute
     * @param $params
     */
    public function validateCategoryID($attribute, $params)
    {
        ModelHelper::validateCategoryID($this, $attribute);
    }
}
