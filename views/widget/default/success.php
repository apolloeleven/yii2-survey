<?php
/**
 * Created by PhpStorm.
 * User: kozhevnikov
 * Date: 05/10/2017
 * Time: 14:24
 */


use kartik\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $survey \onmotion\survey\models\Survey */

$message = $survey->survey_message_completed ? $survey->survey_message_completed : 'Thank you for participating in the survey.';
?>

<p class="text">
	<?php echo \Yii::t('survey', $message); ?>
</p>

