<?php
/**
 * Created by PhpStorm.
 * User: kozhevnikov
 * Date: 10/10/2017
 * Time: 13:59
 */

use onmotion\survey\models\SurveyUserAnswer;
use kartik\slider\Slider;
use vova07\imperavi\Widget;
use yii\bootstrap\Progress;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var $question \onmotion\survey\models\SurveyQuestion */
/** @var $form \yii\widgets\ActiveForm */
/** @var $statIds integer[] */

echo Html::beginTag('div', ['class' => 'answers-stat']);


$answertStats = $question->answers[0]->getTotalUserAnswersCount($statIds);
$totalVotesCount = array_sum(\yii\helpers\ArrayHelper::getColumn($answertStats, 'count'));
$totalScore = 0;
foreach ($answertStats as $stat) {
    try {
        $percent = ($stat['count'] / $totalVotesCount) * 100;
    } catch (\Exception $e) {
        $percent = 0;
    }

    echo $stat['score'];
    echo Progress::widget([
        'id' => 'progress-' . $question->survey_question_id . '-' . $stat['score'],
        'percent' => $percent,
        'label' => $stat['count'],
        'barOptions' => ['class' => 'progress-bar-info init']
    ]);

    $totalScore += $stat['score'] * $stat['count'];
}

$average = $totalScore / $totalVotesCount;

$average = $average > 0 ? round($average, 1) : 0;
echo "average <b>$average</b>";

echo Html::endTag('div');