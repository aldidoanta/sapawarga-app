<?php

namespace app\models;

use app\validator\InputCleanValidator;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "banner".
 *
 * @property int $id
 * @property string $title
 * @property string $image_path
 * @property string $type
 * @property string $link_url
 * @property int $internal_category
 * @property int $internal_entity_id
 * @property int $status
 * @property int $created_by
 * @property int $created_at
 * @property int $updated_by
 * @property int $updated_at
 */

class Banner extends ActiveRecord
{
    const STATUS_DELETED = -1;
    const STATUS_DISABLED = 0;
    const STATUS_ACTIVE = 10;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'banners';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'image_path', 'type', 'link_url', 'internal_category', 'internal_entity_id', 'status'],
                'required'
            ],
            ['title', 'string', 'max' => 200],
            ['title', 'string', 'min' => 10],
            ['title', InputCleanValidator::class],
            [['title', 'image_path', 'type', 'link_url'], 'trim'],
            [['title', 'image_path', 'type', 'link_url'], 'safe'],

            ['link_url', 'url'],

            [['status', 'internal_entity_id'], 'integer'],
            ['status', 'in', 'range' => [-1, 0, 10]],
            ['internal_category', 'in', 'range' => ['survey', 'polling', 'news']],
            ['type', 'in', 'range' => ['internal', 'external']],
        ];
    }

    public function fields()
    {
        $fields = [
            'id',
            'title',
            'image_path',
            'image_path_url' => function () {
                $publicBaseUrl = Yii::$app->params['storagePublicBaseUrl'];
                return "{$publicBaseUrl}/{$this->image_path}";
            },
            'type',
            'link_url',
            'internal_category',
            'internal_entity_id',
            'status',
            'status_label' => function () {
                return $this->getStatusLabel();
            },
            'created_at',
            'updated_at',
        ];

        return $fields;
    }

    protected function getStatusLabel()
    {
        $statusLabel = '';

        switch ($this->status) {
            case self::STATUS_ACTIVE:
                $statusLabel = Yii::t('app', 'status.active');
                break;
            case self::STATUS_DISABLED:
                $statusLabel = Yii::t('app', 'status.inactive');
                break;
            case self::STATUS_DELETED:
                $statusLabel = Yii::t('app', 'status.deleted');
                break;
        }

        return $statusLabel;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Judul',
            'image_path' => 'Image Path',
            'type' => 'Tipe',
            'link_url' => 'URL',
            'internal_category' => 'Internal kategori',
            'internal_entity_id' => 'Internal ID',
            'status' => 'Status',
        ];
    }

    /** @inheritdoc */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => time(),
            ],
            BlameableBehavior::class,
        ];
    }
}
