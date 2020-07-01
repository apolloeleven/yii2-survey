<?php

use yii\db\Migration;

/**
 * Class m200701_091415_alter_survey_is_visible_default
 */
class m200701_091415_alter_survey_is_visible_default extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%survey}}', 'survey_is_visible', $this->tinyInteger(1)->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%survey}}', 'survey_is_visible', $this->tinyInteger(1)->defaultValue(0));
    }
}
