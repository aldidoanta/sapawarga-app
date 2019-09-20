<?php

namespace app\models;

use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "video_featured".
 *
 * @property int $video_id
 * @property int $kabkota_id
 * @property int $seq
 */
class VideoFeatured extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'video_featured';
    }

    public static function primaryKey()
    {
        return ['video_id'];
    }

    public function getVideo()
    {
        return $this->hasOne(Video::class, ['id' => 'video_id']);
    }

    public function getKabkota()
    {
        return $this->hasOne(Area::class, ['id' => 'kabkota_id']);
    }

    protected function getVideoCategory()
    {
        return $this->video->category;
    }

    public function getVideoTitle()
    {
        return $this->video->title;
    }

    public function getVideoSource()
    {
        return $this->video->source;
    }

    public function getVideoUrl()
    {
        return $this->video->video_url;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['video_id', 'required'],
            ['video_id', 'integer'],
            ['seq', 'required'],
            ['seq', 'integer'],
            ['kabkota_id', 'integer'],
        ];
    }

    public function fields()
    {
        return [
            'id' => function () {
                return $this->video->id;
            },
            'title' => 'VideoTitle',
            'source' => 'VideoSource',
            'video_url' => 'VideoUrl',
            'category' => 'VideoCategory',
            'seq',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'video_id'    => 'Video',
            'seq'         => 'Sequence',
        ];
    }

    /** @inheritdoc */
    public function behaviors()
    {
        return [
            [
                'class'              => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value'              => time(),
            ],
            BlameableBehavior::class,
        ];
    }
}
