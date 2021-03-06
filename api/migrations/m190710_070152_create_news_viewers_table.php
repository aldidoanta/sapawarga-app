<?php

use yii\db\Migration;

/**
 * Handles the creation of table news_viewers.
 */
class m190710_070152_create_news_viewers_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('news_viewers', [
            'news_id' => $this->integer()->null(),
            'user_id' => $this->integer()->null(),
            'read_count' => $this->integer()->null(),
        ]);

        // Add composite PK
        $this->addPrimaryKey('news_viewers_pk', 'news_viewers', ['news_id', 'user_id']);

        // Add foreign key for table `tag`
        $this->addForeignKey(
            'fk-news_viewers-news_id',
            'news_viewers',
            'news_id',
            'news',
            'id',
            'CASCADE'
        );

        // Add foreign key for table `tag`
        $this->addForeignKey(
            'fk-news_viewers-user_id',
            'news_viewers',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('news_viewers');
    }
}
