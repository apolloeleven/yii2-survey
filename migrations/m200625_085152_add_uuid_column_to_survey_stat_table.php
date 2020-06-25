<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%survey_stat}}`.
 */
class m200625_085152_add_uuid_column_to_survey_stat_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%survey_stat}}', 'uuid', $this->string(32));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%survey_stat}}', 'uuid');
    }
}
