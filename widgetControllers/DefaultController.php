<?php

namespace onmotion\survey\widgetControllers;

use onmotion\survey\models\search\SurveySearch;
use onmotion\survey\models\search\SurveyStatSearch;
use onmotion\survey\models\Survey;
use onmotion\survey\models\SurveyAnswer;
use onmotion\survey\models\SurveyQuestion;
use onmotion\survey\models\SurveyStat;
use onmotion\survey\models\SurveyType;
use onmotion\survey\Module;
use onmotion\survey\SurveyInterface;
use onmotion\survey\User;
use yii\base\Model;
use yii\base\UserException;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Default controller for the `survey` module
 */
class DefaultController extends Controller
{
    /**
     * @param $question SurveyQuestion
     * @return bool
     */
    protected function validateQuestion($question)
    {
        $userAnswers = $question->userAnswers;
        if ($question->survey_question_type === SurveyType::TYPE_MULTIPLE
            || $question->survey_question_type === SurveyType::TYPE_RANKING
            || $question->survey_question_type === SurveyType::TYPE_MULTIPLE_TEXTBOX
            || $question->survey_question_type === SurveyType::TYPE_CALENDAR
        ) {
            if (count($userAnswers) < 2) {
                return false;
            }
            foreach ($question->answers as $i => $answer) {
                $userAnswer = $userAnswers[$answer->survey_answer_id];
                $userAnswer->validate();
                foreach ($userAnswer->getErrors() as $attribute => $errors) {
                    return false;
                }
                $question->validateMultipleAnswer();
                foreach ($question->userAnswers as $userAnswer) {
                    foreach ($userAnswer->getErrors() as $attribute => $errors) {
                        return false;
                    }
                }
            }
        } elseif ($question->survey_question_type === SurveyType::TYPE_ONE_OF_LIST
            || $question->survey_question_type === SurveyType::TYPE_DROPDOWN
            || $question->survey_question_type === SurveyType::TYPE_SLIDER
            || $question->survey_question_type === SurveyType::TYPE_SINGLE_TEXTBOX
            || $question->survey_question_type === SurveyType::TYPE_COMMENT_BOX
            || $question->survey_question_type === SurveyType::TYPE_DATE_TIME
        ) {
            if (empty(current($userAnswers))) {
                return false;
            }
            $userAnswer = current($userAnswers);
            $userAnswer->validate();
            foreach ($userAnswer->getErrors() as $attribute => $errors) {
                return false;
            }
        }

        return true;
    }

    public function actionDone()
    {
        $singleUserMode = \Yii::$app->session->get('SURVEY_SINGLE_USER_MODE', false);
        $userId = $singleUserMode ? \Yii::$app->session->get('SURVEY_USER_ID') : \Yii::$app->user->getId();

        $singleUserMode = isset($this->module->params['singleUserMode']) ? $this->module->params['singleUserMode'] : false;
        try {
            $id = \Yii::$app->request->post('id');
            if ($id < 0) {
                throw new UserException('Wrong survey id defined');
            }
            $survey = $this->findModel($id);

            $statQuery = SurveyStat::find()
                ->andWhere([
                    'survey_stat_survey_id' => $id,
                    'survey_stat_user_id' => $userId
                ]);

            if ($singleUserMode) {
                $statQuery->andWhere([
                    'uuid' => \Yii::$app->session->get('SURVEY_UUID_' . $id)
                ]);
            }

            $stat = $statQuery->one();
            if ($stat === null) {
                throw new UserException('The requested survey stat does not exist.');
            } else {
                if ($stat->survey_stat_is_done) {
                    throw new UserException('The survey has already been completed.');
                }
            }
            foreach ($survey->questions as $question) {
                if (!$this->validateQuestion($question)) {
                    throw new UserException('An error has been occurred during validating.');
                }
            }
            //all validation is passed.
            $stat->survey_stat_is_done = true;
            $stat->survey_stat_ended_at = new Expression("NOW()");
            $stat->save(false);

//            if ($singleUserMode) {
//                \Yii::$app->user->logout(false);
//            }

            \Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => '<div class="text-center"><h2>' . \Yii::t('survey', 'Thank you!'),
                'content' => $this->renderPartial('@surveyRoot/views/widget/default/success', ['survey' => $survey]),
                'footer' =>
                    Html::button('Ok', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"])
            ];
        } catch (Exception $ex) {
            //Logout user if singleUserMode is enabled
            if ($singleUserMode) {
                \Yii::$app->user->logout(false);
            }

            throw $ex;
        }

    }

    public function actionStart($surveyId)
    {
        $singleUserMode = \Yii::$app->session->get('SURVEY_SINGLE_USER_MODE', false);
        $userId = $singleUserMode ? \Yii::$app->session->get('SURVEY_USER_ID') : \Yii::$app->user->getId();

        \Yii::$app->response->format = Response::FORMAT_JSON;
        $assignedModel = SurveyStat::getAssignedUserStat($userId, $surveyId);
        if (empty($assignedModel)) {
            return [
                'success' => false,
                'error' => \Yii::t('survey', 'User is not assigned to current survey')
            ];
        }

        if ($assignedModel->survey_stat_started_at === null) {
            $assignedModel->survey_stat_started_at = new Expression('NOW()');
        }

        if ($assignedModel->save(false)) {
            return [
                'success' => true
            ];
        } else {
            return [
                'success' => false,
                'error' => \Yii::t('survey', 'Failed to save SurveyState record')
            ];
        }
    }

    protected function findModel($id)
    {
        if (($model = Survey::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


}
