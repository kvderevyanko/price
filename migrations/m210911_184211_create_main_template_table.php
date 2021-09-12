<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%main_tempmlate}}`.
 */
class m210911_184211_create_main_template_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%main_template}}', [
            'id' => $this->primaryKey(),
            'userId' => $this->integer(),
            'name' => $this->string(),
            'code' => $this->text(),
            'date' => $this->integer()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%main_template}}');
    }
}
