<?php

namespace app\models;

use Illuminate\Support\Arr;
use Jdsteam\Sapawarga\Models\Concerns\HasArea;
use Jdsteam\Sapawarga\Models\Concerns\HasAttachments;
use Jdsteam\Sapawarga\Models\Concerns\HasCategory;
use Yii;
use yii\behaviors\TimestampBehavior;
use app\components\ModelHelper;
use app\validator\InputCleanValidator;
use app\validator\IsArrayValidator;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "aspirasi".
 *
 * @property int $id
 * @property int $author_id
 * @property int $category_id
 * @property string $title
 * @property string $description
 * @property int $kabkota_id
 * @property int $kec_id
 * @property int $kel_id
 * @property string $rw
 * @property mixed $attachments
 * @property mixed $meta
 * @property int $status
 * @property string $approval_note
 * @property int $approved_by
 */
class Aspirasi extends ActiveRecord
{
    use HasArea, HasCategory, HasAttachments;

    const STATUS_DELETED = -1;
    const STATUS_DRAFT = 0;

    const STATUS_APPROVAL_REJECTED = 3;
    const STATUS_APPROVAL_PENDING = 5;
    const STATUS_PUBLISHED = 10;

    const CATEGORY_TYPE = 'aspirasi';

    const SCENARIO_USER_CREATE = 'user-create';
    const SCENARIO_USER_UPDATE = 'user-update';

    public function __construct($config = [])
    {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'aspirasi';
    }

    public function getLikes()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])
            ->viaTable('aspirasi_likes', ['aspirasi_id' => 'id']);
    }

    public function getAuthor()
    {
        return $this->hasOne(User::class, ['id' => 'author_id']);
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [
                ['title', 'description', 'kabkota_id', 'kec_id', 'kel_id', 'author_id', 'category_id', 'status'],
                'required',
            ],
            [['title', 'description', 'rw', 'meta'], 'trim'],
            ['description', 'string', 'max' => 1024 * 3],
            ['description', InputCleanValidator::class],
            [['author_id', 'category_id', 'kabkota_id', 'kec_id', 'kel_id', 'status'], 'integer'],
            ['meta', 'default'],
            ['approved_by', 'default'],
            ['approved_at', 'default'],
            ['status', 'in', 'range' => [0, 5], 'on' => self::SCENARIO_USER_CREATE],
            ['status', 'in', 'range' => [0, 5], 'on' => self::SCENARIO_USER_UPDATE],
        ];

        return array_merge(
            $rules,
            $this->rulesTitle(),
            $this->rulesApprovalNote(),
            $this->rulesRw(),
            $this->rulesCategory(),
            $this->rulesAttachments()
        );
    }

    protected function rulesTitle()
    {
        return [
            ['title', 'string', 'max' => 255],
            ['title', 'string', 'min' => 5],
            ['title', InputCleanValidator::class],
        ];
    }

    protected function rulesApprovalNote()
    {
        return [
            ['approval_note', 'default'],
            [
                'approval_note',
                'required',
                'when' => function ($model) {
                    return $model->status === self::STATUS_APPROVAL_REJECTED;
                },
            ]
        ];
    }

    public function fields()
    {
        return [
            'id',
            'author_id',
            'author' => 'AuthorField',
            'category_id',
            'category' => 'CategoryField',
            'title',
            'description',
            'kabkota_id',
            'kabkota' => 'KabkotaField',
            'kec_id',
            'kecamatan' => 'KecamatanField',
            'kel_id',
            'kelurahan'  => 'KelurahanField',
            'likes_count'  => 'LikesCount',
            'likes_users' => 'LikesUsers',
            'rw',
            'meta',
            'status',
            'status_label' => 'StatusLabel',
            'approval_note',
            'attachments'  => 'AttachmentsField',
            'created_at',
            'updated_at',
        ];
    }

    protected function getAuthorField()
    {
        $publicBaseUrl = Yii::$app->params['storagePublicBaseUrl'];

        return [
            'id'         => $this->author->id,
            'name'       => $this->author->name,
            'photo_url'  => $this->author->photo_url,
            'photo_url_full' => $this->author->photo_url ? "$publicBaseUrl/{$this->author->photo_url}" : null,
            'role_label' => $this->author->getRoleLabel(),
            'email'      => $this->author->email,
            'phone'      => $this->author->phone,
            'address'    => $this->author->address,
        ];
    }

    protected function getLikesCount()
    {
        return (int)$this->getLikes()->count();
    }

    protected function getLikesUsers()
    {
        return array_map(function ($item) {
            return [
                'id'   => $item->id,
                'name' => $item->name,
            ];
        }, $this->likes);
    }

    protected function getStatusLabel()
    {
        if (in_array($this->status, [
            self::STATUS_DRAFT,
            self::STATUS_PUBLISHED,
            self::STATUS_APPROVAL_PENDING,
            self::STATUS_APPROVAL_REJECTED])
        ) {
            return $this->getStatusAspirasi();
        }

        return $this->getStatusCommon();
    }

    private function getStatusAspirasi()
    {
        $statusLabel = '';

        switch ($this->status) {
            case self::STATUS_PUBLISHED:
                $statusLabel = Yii::t('app', 'status.published');
                break;
            case self::STATUS_APPROVAL_PENDING:
                $statusLabel = Yii::t('app', 'status.approval-pending');
                break;
            case self::STATUS_APPROVAL_REJECTED:
                $statusLabel = Yii::t('app', 'status.approval-rejected');
                break;
            case self::STATUS_DRAFT:
                $statusLabel = Yii::t('app', 'status.draft');
                break;
        }

        return $statusLabel;
    }

    private function getStatusCommon()
    {
        $statusLabel = '';

        switch ($this->status) {
            case self::STATUS_DELETED:
                $statusLabel = Yii::t('app', 'status.deleted');
                break;
        }

        return $statusLabel;
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
        ];
    }

    /** @inheritdoc */
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->author_id = Yii::$app->user->getId();
        }

        if ($this->status == self::STATUS_PUBLISHED) {
            $this->approval_note = null;
        }

        return parent::beforeSave($insert);
    }
}
