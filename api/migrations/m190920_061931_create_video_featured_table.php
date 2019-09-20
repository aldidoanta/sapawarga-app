<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%video_featured}}`.
 */
class m190920_061931_create_video_featured_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%video_featured}}', [
            'video_id'    => $this->integer()->notNull(),
            'kabkota_id'  => $this->integer(),
            'seq'         => $this->integer()->notNull(),
            'created_at'  => $this->integer()->null(),
            'created_by'  => $this->integer()->null(),
            'updated_at'  => $this->integer()->null(),
            'updated_by'  => $this->integer()->null(),
        ]);

        $this->addForeignKey(
            'fk-video_featured-video_id',
            'video_featured',
            'video_id',
            'videos',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-video_featured-video_id',
            'video_featured'
        );

        $this->dropTable('{{%video_featured}}');
    }
}
