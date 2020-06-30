<?php
/**
 * Created by PhpStorm.
 * User: kozhevnikov
 * Date: 10/10/2017
 * Time: 13:59
 */

use onmotion\survey\models\SurveyUserAnswer;
use yii\bootstrap\Progress;
use yii\helpers\Html;


/** @var $question \onmotion\survey\models\SurveyQuestion */
/** @var $form \yii\widgets\ActiveForm */
/** @var $statIds integer[] */

$totalVotesCount = $question->getTotalUserAnswersCount($statIds);

echo Html::beginTag('div', ['class' => 'answers-stat']);
foreach ($question->answers as $i => $answer) {
    $count = $answer->getTotalUserAnswersCount($statIds);
    try {
        $percent = ($count / $totalVotesCount) * 100;
    }catch (\Exception $e){
        $percent = 0;
    }
    echo $answer->survey_answer_name;
    echo Progress::widget([
        'id' => 'progress-' . $answer->survey_answer_id,
        'percent' => $percent,
        'label' => $count,
        'barOptions' => ['class' => 'progress-bar-info init']
    ]);
}
echo Html::endTag('div');