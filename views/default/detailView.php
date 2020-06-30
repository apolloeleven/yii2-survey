<?php
/**
 * Created by PhpStorm.
 * User: kozhevnikov
 * Date: 05/10/2017
 * Time: 14:24
 */

use cenotia\components\modal\RemoteModal;
use onmotion\survey\models\SurveyStat;
use kartik\editable\Editable;
use kartik\helpers\Html;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\bootstrap\BootstrapPluginAsset;
use yii\bootstrap\Modal;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $survey \onmotion\survey\models\Survey */
/* @var $stat SurveyStat */
/* @var $displayStatusInfo bool */


BootstrapPluginAsset::register($this);
?>

    <div id="survey-widget">
        <div class="row">
            <div class="col-sm-12">
                <div class="survey-container">
                    <div id="survey-widget-title">
                        <h3><?php echo $survey->survey_name; ?> - <?php echo \Yii::$app->formatter->asDate($stat->survey_stat_ended_at); ?></h3>
                    </div>

                    <div id="survey-questions">
                        <?php
                        var_dump($survey->questions[0]);
                        foreach ($survey->questions as $i => $question) {
                            echo $this->render('@surveyRoot/views/widget/question/_form', [
                                'question' => $question,
	                            'number' => $i,
	                            'readonly' => true
	                        ]);
                        }
                        ?>
                    </div>
                </div>
                <div class="loader"></div>

            </div>
        </div>
    </div>
<?php
$this->registerJs(<<<JS
$(document).ready(function(e) {
    setTimeout(function() {
       $('.progress-bar').each(function(i, el) {
    if ($(el).hasClass('init')){
        $(el).removeClass('init');
    }
  })
    }, 1000);
});
JS
);

$this->registerJs(<<<JS
$(document).ready(function (e) {
    $.fn.survey();
});
JS
);
?>