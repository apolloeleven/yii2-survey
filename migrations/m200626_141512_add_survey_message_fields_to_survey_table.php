<?php

use yii\db\Migration;

/**
 * Class m200626_141512_add_survey_message_fields_to_survey_table
 */
class m200626_141512_add_survey_message_fields_to_survey_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%survey}}', 'survey_message_completed', $this->text());
        $this->addColumn('{{%survey}}', 'survey_message_closed', $this->text());
        $this->addColumn('{{%survey}}', 'survey_message_not_allowed', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{survey}}', 'survey_message_not_allowed');
        $this->dropColumn('{{survey}}', 'survey_message_closed');
        $this->dropColumn('{{survey}}', 'survey_message_completed');
    }
}
