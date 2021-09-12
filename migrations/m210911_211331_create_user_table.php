<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user}}`.
 */
class m210911_211331_create_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(),
            'password' => $this->string(),
            'name' => $this->string(),
        ]);

        $users = [
            ['Sveta', 'sveta', 'Света'],
            ['Misha', 'misha', 'Миша'],
            ['Masha', 'masha', 'Маша'],
            ['Petya', 'petya', 'Петя'],
            ['Lesha', 'lesha', 'Лёша'],
            ['Kolya', 'kolya', 'Коля'],
            ['Alena', 'alena', 'Алёна'],
        ];

        foreach ($users as $user) {
            $this->insert('{{%user}}', [
                'username' => $user[0],
                'password' => $user[1],
                'name' => $user[2],
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user}}');
    }
}
