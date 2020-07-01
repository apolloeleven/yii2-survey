<?php

namespace onmotion\survey;

use yii\base\InvalidConfigException;
use yii\base\UserException;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

/**
 * survey module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace;
    public $userClass;

    public $singleUserMode;
    public $user;

    public $params = [
        'uploadsUrl' => null,
        'uploadsPath' => null,
        'singleUserMode' => false,
        'username' => false,
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {

        if (empty($this->controllerNamespace)) {
            $this->controllerNamespace = \Yii::$app->controllerNamespace === 'backend\controllers'
                ? 'onmotion\survey\controllers'
                : 'onmotion\survey\widgetControllers';
        }

        parent::init();

        if (empty($this->params['uploadsUrl'])) {
            throw new InvalidConfigException("You must set uploadsUrl param in the config. Please see the documentation for more information.");
        } else {
            $this->params['uploadsUrl'] = rtrim($this->params['uploadsUrl'], '/');
        }
        if (empty($this->params['uploadsPath'])) {
            throw new InvalidConfigException("You must set uploadsPath param in the config. Please see the documentation for more information.");
        } else {
            $this->params['uploadsPath'] = FileHelper::normalizePath($this->params['uploadsPath']);
        }

        if (empty($this->params['singleUserMode'])) {
            $this->params['singleUserMode'] = false;
        }

        if (!is_bool($this->params['singleUserMode'])) {
            throw new InvalidConfigException("singleUserMode param must be a boolean");
        }

        if ($this->params['singleUserMode'] && !isset($this->params['username'])) {
            throw new InvalidConfigException("You must must provide username param when singleUserMode is set to true");
        }

        if($this->params['singleUserMode'] && !is_string($this->params['username'])) {
            throw new InvalidConfigException("username param must be a string");
        }

        $this->singleUserMode = $this->params['singleUserMode'];

        $this->userClass = \Yii::$app->user->identityClass;
        if($this->singleUserMode && $this->params['username']) {
            $this->user = $this->userClass::findByUsername($this->params['username']);
            if(!$this->user) {
                throw new InvalidConfigException("Survey User not found, check username param");
            }
        }

        \Yii::setAlias('@surveyRoot', __DIR__);

        // set up i8n
        if (empty(\Yii::$app->i18n->translations['survey'])) {
            \Yii::$app->i18n->translations['survey'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@surveyRoot/messages',
            ];
        }

        $view = \Yii::$app->getView();
        SurveyAsset::register($view);
    }
}
