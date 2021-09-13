<?php

use yii\db\Migration;

/**
 * Class m210913_194552_update_user_table
 */
class m210913_194552_update_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->update('{{%user}}', [
            'username' => 'Nastya',
            'password' => 'nastya123',
            'name' => "Настя",
        ], [
            'username' => 'Alena'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210913_194552_update_user_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210913_194552_update_user_table cannot be reverted.\n";

        return false;
    }
    */
}
