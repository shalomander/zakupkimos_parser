<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%purchase}}`.
 */
class m210113_094229_create_purchase_list_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%purchase_list}}', [
            'id' => $this->primaryKey(),
            'auction_id' => $this->integer(),
            'need_id' => $this->integer(),
            'tender_id' => $this->integer(),
            'trade_type' => $this->integer(),
            'number' => $this->integer(),
            'name' => $this->text()->notNull(),
            'customers' => $this->text(),
            'purchase_creator_name' => $this->string(),
            'purchase_creator_inn' => $this->string(),
            'purchase_creator_supplier_id' => $this->integer(),
            'purchase_creator_customer_id' => $this->integer(),
            'purchase_creator_id' => $this->integer(),
            'has_publisher' => $this->boolean(),
            'state_name' => $this->string(),
            'state_id' => $this->integer(),
            'start_price' => $this->money(20,2),
            'region_name' => $this->string(),
            'offer_count' => $this->integer(),
            'auction_current_price' => $this->money(20,2),
            'auction_next_price' => $this->money(20,2),
            'begin_date' => $this->integer(),
            'end_date' => $this->integer(),
            'federal_law_name' => $this->string(),
            'region_path' => $this->string(),
            'is_external_integration' => $this->boolean(),
            'purchase_id' => $this->string(),
            'delivery_place' => $this->text(),
            'is_notified' => $this->boolean()
        ],'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%purchase_list}}');
    }
}
