<?php
/**
 * Created by PhpStorm.
 * User: kozhevnikov
 * Date: 05/10/2017
 * Time: 14:24
 */

use cenotia\components\modal\RemoteModal;
use kartik\select2\Select2;
use onmotion\survey\models\search\SurveyStatSearch;
use kartik\editable\Editable;
use kartik\helpers\Html;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\bootstrap\BootstrapPluginAsset;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $survey \onmotion\survey\models\Survey */
/* @var $respondentsCount integer */
/* @var $withUserSearch boolean */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $surveyStatIds \yii\data\ActiveDataProvider */

$this->title = Yii::t('survey', 'Survey') . ' - ' . $survey->survey_name;

BootstrapPluginAsset::register($this);


?>
    <div id="survey-view">
        <div id="survey-title">
            <div class="subcontainer flex">
                <h4><?= $survey->survey_name; ?></h4>
                <div>
                    <div class="survey-labels">
                        <a href="<?= Url::toRoute(['default/update/', 'id' => $survey->survey_id]) ?>"
                           class="btn btn-info btn-xs survey-label" data-pjax="0"
                           title="edit"><span class="glyphicon glyphicon-pencil"></span></a>
<!--                        <span class="survey-label btn btn-info btn-xs respondents-toggle"-->
<!--                              data-toggle="tooltip"-->
<!--                              title="--><?php //echo \Yii::t('survey', 'Respondents') ?><!--">-->
<!--                            --><?php //echo \Yii::t('survey', 'Number of respondents') ?><!--:-->
<!--                            --><?php //echo $survey->getRespondentsCount() ?>
<!--                        </span>-->
                        <span class="survey-label btn btn-info btn-xs" data-toggle="tooltip"
                              title="<?= \Yii::t('survey', 'Questions') ?>"><?= \Yii::t('survey', 'Questions') ?>: <?= $survey->getQuestions()->count() ?></span>
<!--                        <span class="survey-label btn btn-info btn-xs restricted-users-toggle"-->
<!--                              data-toggle="tooltip"-->
<!--                              title="--><?php //echo \Yii::t('survey', 'Restricted users') ?><!--">-->
<!--                            --><?php //echo \Yii::t('survey', 'Restricted users') ?><!--:-->
<!--                            --><?php //echo $survey->getRestrictedUsersCount() ?>
<!--                        </span>-->
                    </div>

                </div>

            </div>
            <div class="subcontainer">
                <?php
                echo Html::beginTag('div', ['class' => 'row']);
                echo Html::beginTag('div', ['class' => 'col-md-6']);
                echo Html::label(Yii::t('survey', 'Expired at') . ': ', 'survey-survey_expired_at');
                echo Editable::widget([
                    'model' => $survey,
                    'attribute' => 'survey_expired_at',
                    'header' => 'Expired at',
                    'asPopover' => true,
                    'size' => 'md',
                    'inputType' => Editable::INPUT_DATETIME,
                    'formOptions' => [
                        'action' => Url::toRoute(['default/update-editable', 'property' => 'survey_expired_at'])
                    ],
                    'additionalData' => ['id' => $survey->survey_id],
                    'options' => [
                        'class' => Editable::INPUT_DATETIME,
                        'pluginOptions' => [
                            'autoclose' => true,
                            // 'format' => 'd.m.Y H:i'
                        ],
                        'options' => ['placeholder' => 'Expired at']
                    ]
                ]);
                echo Html::endTag('div');

                echo Html::beginTag('div', ['class' => 'col-md-6']);
                echo Html::endTag('div');
                echo Html::endTag('div');

                Pjax::begin([
                    'id' => 'survey-pjax',
                    'enablePushState' => false,
                    'timeout' => 0,
                    'scrollTo' => false,
                    'clientOptions' => [
                        'type' => 'post',
                        'skipOuterContainers' => true,
                    ]
                ]);

                $form = ActiveForm::begin([
                    'id' => 'survey-form',
                    'action' => Url::toRoute(['default/update', 'id' => $survey->survey_id]),
                    'options' => ['class' => 'form-inline', 'data-pjax' => true],
                    'enableClientValidation' => false,
                    'enableAjaxValidation' => false,
                    'fieldConfig' => [
                        'template' => "<div class='survey-form-field'>{label}{input}\n{error}</div>",
                        'labelOptions' => ['class' => ''],
                    ],
                ]);

                echo Html::beginTag('div', ['class' => 'row']);
                echo Html::beginTag('div', ['class' => 'col-md-12']);
                echo $form->field($survey, "survey_descr", ['template' => "<div class='survey-form-field'>{label}{input}</div>",]
                )->textarea(['rows' => 3]);
                echo Html::tag('div', '', ['class' => 'clearfix']);
                echo Html::endTag('div');
                echo Html::endTag('div');

                echo Html::beginTag('div', ['class' => 'row']);
                echo Html::beginTag('div', ['class' => 'col-md-3']);
                echo $form->field($survey, "survey_is_closed", ['template' => "<div class='survey-form-field submit-on-click'>{input}{label}</div>",]
                )->checkbox(['class' => 'checkbox danger'], false);
                echo Html::tag('div', '', ['class' => 'clearfix']);
                echo $form->field($survey, "survey_is_pinned", ['template' => "<div class='survey-form-field submit-on-click'>{input}{label}</div>",]
                )->checkbox(['class' => 'checkbox'], false);
                echo Html::tag('div', '', ['class' => 'clearfix']);
                echo $form->field($survey, "survey_is_visible", ['template' => "<div class='survey-form-field submit-on-click'>{input}{label}</div>",]
                )->checkbox(['class' => 'checkbox'], false);
                if ($withUserSearch) {
                    echo Html::tag('div', '', ['class' => 'clearfix']);
                    echo $form->field($survey,
                        "survey_is_private",
                        ['template' => "<div class='survey-form-field submit-on-click'>{input}{label}</div>",]
                    )->checkbox(['class' => 'checkbox danger'], false);
                }
                echo Html::endTag('div');

                echo Html::beginTag('div', ['class' => 'col-md-9']);
                echo $form->field($survey, "survey_tags")->input('text', ['placeholder' => 'Comma separated']);
                echo Html::endTag('div');
                echo Html::endTag('div');

                echo Html::submitButton('', ['class' => 'hidden']);
                echo Html::tag('div', '', ['class' => 'clearfix']);

                ActiveForm::end();

                Pjax::end();
                ?>

            </div>

        </div>
        <div>
            <?php Pjax::begin(['id' => 'survey-stats-table']) ?>
            <div class="container">
                <?php echo \yii\grid\GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        [
                            'contentOptions' => ['style' => 'min-width: 300px; white-space: nowrap;'],
                            'attribute' => 'survey.survey_name',
                            'filter' => false,
                            'format' => 'raw',
                            'label' => Yii::t('survey', 'Survey'),
                            /** @var $model SurveyStatSearch */
                            'value' => function ($model) {
                                return Html::a($model->survey->survey_name, Url::to(['default/detail-view', 'statId' => $model->survey_stat_id]),
                                    ['class' => 'survey-stat-link', 'data' => [ 'pjax' => '0']]);
                            }
                        ],
                        [
                            'headerOptions' => ['style' => 'min-width:300px'],
                            'attribute' => 'survey_stat_ended_at',
                            'label' => Yii::t('survey', 'Survey Date'),
                            'format' => 'date',
                            'filter' => \kartik\date\DatePicker::widget([
                                'model' => $searchModel,
                                'attribute' => 'survey_stat_ended_at',
                                'pluginOptions' => [
                                    'autoclose' => true,
                                    'format' => 'yyyy-mm-dd'
                                ]
                            ])
                        ],
                        [
                            'attribute' => 'commentText',
                            'label' => 'Comment',
                            /** @var $model SurveyStatSearch */
                            'value' => function ($model) {
                                foreach ($model->surveyUserAnswers as $answerData) {
                                    if ($answerData->question->survey_question_type === \onmotion\survey\models\SurveyType::TYPE_COMMENT_BOX) {
                                        return $answerData->survey_user_answer_text;
                                    }
                                }
                                return '';
                            }
                        ]
                    ],
                ]) ?>
            </div>
            <?php Pjax::end(); ?>

            <?php Pjax::begin(['id' => 'survey-filtered-stats']) ?>
            <div class="survey-container">
                <h3 style="margin-left: 20px"><?php echo \Yii::t('survey', 'Number of respodents') . ':' ?>
                    <b><?php echo $respondentsCount; ?></b></h3>
                <div id="survey-questions">
                    <?php if ($respondentsCount > 0) {
                        foreach ($survey->questions as $i => $question) {
                            echo $this->render('/question/_viewForm', [
                                'question' => $question,
                                'number' => $i,
                                'statIds' => $surveyStatIds
                            ]);
                        }
                    }
                    ?>
                </div>
            </div>
            <?php Pjax::end(); ?>


        </div>
    </div>

    <div class="hidden-modal-right " id="respondents-modal">
        <div class="close-btn">&times;</div>
        <?php

        $surveyId = $survey->survey_id;

        echo $this->render('respondents',
            compact('searchModel', 'dataProvider', 'surveyId', 'withUserSearch'));
        ?>
    </div>

    <div class="hidden-modal-right " id="restricted-users-modal">
        <div class="close-btn">&times;</div>
        <?php

        $surveyId = $survey->survey_id;

        echo $this->render('restrictedUsers',
            compact('searchModel', 'restrictedUserDataProvider', 'surveyId', 'withUserSearch'));
        ?>
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