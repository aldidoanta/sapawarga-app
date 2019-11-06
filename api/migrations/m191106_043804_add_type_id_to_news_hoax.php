<?php

use app\components\CustomMigration;
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
        $this->dropColumn('news_hoax', 'type_id');
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
        // Update news_hoax's category value to default value
        // Remove old categories that have been moved to type_id
    }
}
