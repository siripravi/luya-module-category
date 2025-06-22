<?php

use yii\db\Migration;

/**
 * Class m200404_173843_category_table
 */
class m200404_173843_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('category', [
            'id'            => $this->primaryKey(),
            'name'         => $this->string()->notNull(),
            'root'          => $this->integer()->notNull(),
            'lft'           => $this->integer()->notNull(),
            'rgt'           => $this->integer()->notNull(),
            'level'         => $this->integer()->notNull()->defaultValue(0),
            'slug'          => $this->string()->notNull(),
            'icon'          => $this->string()->notNull(),
            'icon_type'     => $this->integer(),
            'is_deleted'    => $this->tinyInteger(1)->notNull()->defaultValue(0),
            'created_at'    => $this->integer()->notNull(),
            'updated_at'    => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200404_173843_category_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200404_173843_category_table cannot be reverted.\n";

        return false;
    }
    */
}
