<?php

use app\components\CustomMigration;
use app\models\Category;
use app\models\NewsHoax;

/**
 * Class m191106_043804_add_type_id_to_news_hoax */
class m191106_043804_add_type_id_to_news_hoax extends CustomMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('news_hoax', 'type_id', $this->integer()->notNull()->after('category_id'));
        $this->assignTypeId();
        $this->refactorNewsHoaxCategory();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191106_043804_add_type_id_to_news_hoax cannot be reverted.\n";

        return false;
    }

    private function assignTypeId()
    {
        $hoaxTypes = include __DIR__ . '/../config/references/hoax_types.php';
        $newsHoaxArray = NewsHoax::find()
            ->leftJoin('categories', '`categories`.`id` = `news_hoax`.`category_id`')
            ->select(['news_hoax.id', 'categories.name'])
            ->asArray()
            ->all();

        $hoaxTypeTitleColumn = array_column($hoaxTypes, 'title');
        foreach ($newsHoaxArray as $newsHoaxItem) {
            $hoaxTypeIndex = array_search($newsHoaxItem['name'], $hoaxTypeTitleColumn);
            $typeId = $hoaxTypes[$hoaxTypeIndex]['id'];
            $this->update('news_hoax', ['type_id' => $typeId], ['id' => $newsHoaxItem['id']]);
        }
    }

    private function refactorNewsHoaxCategory()
    {
        // Insert new categories for news_hoax
        $this->insertNewCategories();

        // Update news_hoax's category value to default value
        $defaultCategoryModel = Category::find()
            ->select('id')
            ->where(['type' => 'newsHoax', 'name' => 'Lainnya'])
            ->asArray()
            ->one();
        $this->update('news_hoax', ['category_id' => $defaultCategoryModel['id']]);

        // Remove old categories that have been moved to type_id
        $hoaxTypes = include __DIR__ . '/../config/references/hoax_types.php';
        $hoaxTypeTitleColumn = array_column($hoaxTypes, 'title');
        $this->delete(
            'categories',
            [
                'and',
                ['type' => NewsHoax::CATEGORY_TYPE],
                ['in', 'name', $hoaxTypeTitleColumn]
            ]
        );
    }

    private function insertNewCategories()
    {
        $type = NewsHoax::CATEGORY_TYPE;
        $status = Category::STATUS_ACTIVE;
        $now = time();

        $this->batchInsert(
            'categories',
            [
                'type', 'name', 'meta', 'status', 'created_at', 'updated_at',
            ],
            [
                [$type, 'Regulasi/Hukum', null, $status, $now, $now,],
                [$type, 'Lalu Lintas', null, $status, $now, $now,],
                [$type, 'SARA', null, $status, $now, $now,],
                [$type, 'Bencana', null, $status, $now, $now,],
                [$type, 'Kriminalitas', null, $status, $now, $now,],
                [$type, 'Kesehatan', null, $status, $now, $now,],
                [$type, 'Peristiwa Tidak Lazim', null, $status, $now, $now,],
                [$type, 'Daily Lives', null, $status, $now, $now,],
                [$type, 'Politik', null, $status, $now, $now,],
                [$type, 'Penipuan', null, $status, $now, $now,],
                [$type, 'Opini', null, $status, $now, $now,],
                [$type, 'Lainnya', null, $status, $now, $now,],
            ]
        );
    }
}
