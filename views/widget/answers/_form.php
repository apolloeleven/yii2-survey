<?php
/**
 * Created by PhpStorm.
 * User: kozhevnikov
 * Date: 10/10/2017
 * Time: 13:37
 */

/** @var $question \onmotion\survey\models\SurveyQuestion */
/** @var $form \yii\widgets\ActiveForm */
/** @var $readonly boolean */
/** @var $stat \onmotion\survey\models\SurveyStat */

echo $this->render('@surveyRoot/views/widget/answers/' . $question->survey_question_type, [
    'question' => $question,
    'form' => $form,
    'readonly' => $readonly,
    'stat' => isset($stat) ? $stat : null
]);