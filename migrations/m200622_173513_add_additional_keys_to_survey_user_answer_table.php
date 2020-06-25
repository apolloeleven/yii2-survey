<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%survey_user_answer}}`.
 */
class m200622_173513_add_additional_keys_to_survey_user_answer_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%survey_user_answer}}', 'uuid', $this->string(32));
        $this->addColumn('{{%survey_user_answer}}', 'survey_user_answer_survey_stat_id', $this->integer()->unsigned());
        $this->addForeignKey('fk_survey_user_answer_survey_stat',
            '{{%survey_user_answer}}',
            'survey_user_answer_survey_stat_id',
            '{{%survey_stat}}',
            'survey_stat_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_survey_user_answer_survey_stat', '{{%survey_user_answer}}');
        $this->dropColumn('{{%survey_user_answer}}', 'survey_user_answer_survey_stat_id');
        $this->dropColumn('{{%survey_user_answer}}', 'uuid');
    }
}
