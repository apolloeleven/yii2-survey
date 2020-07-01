<?php
/**
 * Created by PhpStorm.
 * User: kozhevnikov
 * Date: 26/10/2017
 * Time: 10:09
 */

namespace onmotion\survey;


use onmotion\survey\models\SurveyStat;
use yii\db\Exception;
use yii\db\Expression;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class Survey extends \yii\base\Widget
{
    public $surveyId = null;
    public $username = null;
    public $autoStart = true;
    public $displayStatusInfo = true;

    public function init()
    {
        // set up i8n
        if (empty(\Yii::$app->i18n->translations['survey'])) {
            \Yii::$app->i18n->translations['survey'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@surveyRoot/messages',
            ];
        }

        \Yii::setAlias('@surveyRoot', __DIR__);

        if ($this->username) {
            $user = \Yii::$app->user->identityClass::findByUsername($this->username);
            if (!$user) {
                throw new \yii\base\Exception(\Yii::t('survey', 'Survey user was not found. check "username" param'));
            }
            \Yii::$app->session->set('SURVEY_SINGLE_USER_MODE', true);
            \Yii::$app->session->set('SURVEY_USER_ID', $user->getId());

            if (!\Yii::$app->session->get('SURVEY_UUID_' . $this->surveyId)) {
                \Yii::$app->session->set('SURVEY_UUID_' . $this->surveyId, \Yii::$app->security->generateRandomString());
            }
        }

        parent::init();
    }

    public function getViewPath()
    {
        return \Yii::getAlias('@surveyRoot/views');
    }

    public function beforeRun()
    {
        return parent::beforeRun();
    }

    public function run()
    {
        $view = $this->getView();
        SurveyWidgetAsset::register($view);

        $survey = $this->findModel($this->surveyId);
        if (!$survey || !$survey->isAccessibleByCurrentUser) {
            return $this->renderUnavailable($survey);
        }

        $status = $survey->getStatus();
        if ($status !== 'active') {
            return $this->renderClosed($survey);
        }

        $singleUserMode = \Yii::$app->session->get('SURVEY_SINGLE_USER_MODE', false);
        $userId = $singleUserMode ? \Yii::$app->session->get('SURVEY_USER_ID') : \Yii::$app->user->getId();

        $assignedModel = SurveyStat::getAssignedUserStat($userId, $this->surveyId);
        if (empty($assignedModel)) {
            SurveyStat::assignUser($userId, $this->surveyId);
            $assignedModel = SurveyStat::getAssignedUserStat($userId, $this->surveyId);
        }

        if ($this->autoStart == true) {
            if ($assignedModel->survey_stat_started_at === null) {
                $assignedModel->survey_stat_started_at = new Expression('NOW()');
            }
        }

        $assignedModel->save(false);

        return $this->renderSurvey($this->surveyId, $assignedModel);
    }

    private function renderClosed($survey)
    {
        echo $this->render('widget/default/closed', ['survey' => $survey]);
    }

    private function renderUnavailable($survey)
    {
        echo $this->render('widget/default/unavailable', ['survey' => $survey]);
    }

    private function renderSurvey($id, $stat)
    {
        $survey = $this->findModel($id);
        echo $this->render('widget/default/index', [
            'survey' => $survey,
            'stat' => $stat,
            'displayStatusInfo' => $this->displayStatusInfo]
        );
    }

    protected function findModel($id)
    {
        if (($model = \onmotion\survey\models\Survey::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}