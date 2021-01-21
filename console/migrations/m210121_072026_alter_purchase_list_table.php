<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%purchase_list}}`.
 */
class m210121_072026_alter_purchase_list_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%purchase_list}}', 'created_at', $this->timestamp()->defaultExpression('NOW()'));
        $this->addColumn('{{%purchase_list}}', 'status_id', $this->integer()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%purchase_list}}', 'created_at');
        $this->dropColumn('{{%purchase_list}}', 'status_id');
    }
}
