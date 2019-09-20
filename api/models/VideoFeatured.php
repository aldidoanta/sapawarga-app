<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "video_featured".
 *
 * @property int $id
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
        return $this->video->channel;
    }

    public function getVideoTitle()
    {
        return $this->video->title;
    }

    public function getVideoContent()
    {
        return $this->video->content;
    }

    public function getVideoCoverPathUrl()
    {
        $bucket = Yii::$app->fileStorage->getBucket('imageFiles');
        return $bucket->getFileUrl($this->video->cover_path);
    }

    public function getVideoSourceDate()
    {
        return $this->video->source_date;
    }

    public function getVideoSourceUrl()
    {
        return $this->video->source_url;
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
            'content' => 'VideoContent',
            'cover_path_url' => 'VideoCoverPathUrl',
            'source_date' => 'VideoSourceDate',
            'source_url' => 'VideoSourceUrl',
            'channel' => 'VideoChannel',
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
            'video_id'     => 'Berita',
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
