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
        $this->InsertNewCategories();
        // Update news_hoax's category value to default value
        $defaultCategoryModel = Category::find()
            ->select('id')
            ->where(['type' => 'newsHoax', 'name' => 'Lainnya'])
            ->asArray()
            ->one();
        // NewsHoax::updateAll()
        // Remove old categories that have been moved to type_id
    }

    private function InsertNewCategories()
    {
        $type = NewsHoax::CATEGORY_TYPE;
        $status = Category::STATUS_ACTIVE;
        $now = time();

        $this->insert('categories', [
            'type'       => $type,
            'name'       => 'Regulasi/Hukum',
            'meta'       => null,
            'status'     => $status,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->insert('categories', [
            'type'       => $type,
            'name'       => 'Lalu Lintas',
            'meta'       => null,
            'status'     => $status,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->insert('categories', [
            'type'       => $type,
            'name'       => 'SARA',
            'meta'       => null,
            'status'     => $status,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->insert('categories', [
            'type'       => $type,
            'name'       => 'Bencana',
            'meta'       => null,
            'status'     => $status,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->insert('categories', [
            'type'       => $type,
            'name'       => 'Kriminalitas',
            'meta'       => null,
            'status'     => $status,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->insert('categories', [
            'type'       => $type,
            'name'       => 'Kesehatan',
            'meta'       => null,
            'status'     => $status,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->insert('categories', [
            'type'       => $type,
            'name'       => 'Peristiwa Tidak Lazim',
            'meta'       => null,
            'status'     => $status,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->insert('categories', [
            'type'       => $type,
            'name'       => 'Daily Lives',
            'meta'       => null,
            'status'     => $status,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->insert('categories', [
            'type'       => $type,
            'name'       => 'Politik',
            'meta'       => null,
            'status'     => $status,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->insert('categories', [
            'type'       => $type,
            'name'       => 'Penipuan',
            'meta'       => null,
            'status'     => $status,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->insert('categories', [
            'type'       => $type,
            'name'       => 'Opini',
            'meta'       => null,
            'status'     => $status,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->insert('categories', [
            'type'       => $type,
            'name'       => 'Lainnya',
            'meta'       => null,
            'status'     => $status,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
