<?php

namespace yiicod\hauth\controllers;

use CEvent;
use CLogger;
use CMap;
use Exception;
use Hybrid_Exception;
use Yii;

/**
 * Class SocialAuthController
 * Controller for work with social networks: login, process callbacks etc.
 *
 */
class SocialAuthController extends SocialAuthBase
{

    /**
     * Init method
     */
    public function init()
    {
        parent::init();
        $module = Yii::app()->controller->module === null ? 'default' : Yii::app()->controller->module->id;
        $this->layout = $this->filterParam(@Yii::app()->getComponent('hauth')->controllers[$module][Yii::app()->controller->id]['layout'], '');
    }

    /**
     * @return array|mixed
     */
    public function filters()
    {
        $module = Yii::app()->controller->module === null ? 'default' : Yii::app()->controller->module->id;
        $value = @Yii::app()->getComponent('hauth')->controllers[$module][Yii::app()->controller->id]['filters'];
        return CMap::mergeArray(parent::filters(), $this->filterParam(@Yii::app()->getComponent('hauth')->controllers[$module][Yii::app()->controller->id]['filters'], array())
        );
    }

    /**
     * @return array|mixed
     */
    public function accessRules()
    {
        $module = Yii::app()->controller->module === null ? 'default' : Yii::app()->controller->module->id;
        return CMap::mergeArray(parent::accessRules(), $this->filterParam(@Yii::app()->getComponent('hauth')->controllers[$module][Yii::app()->controller->id]['accessRules'], array())
        );
    }

    /**
     * Connect action.
     * @param array $provider
     */
    public function actionConnect($provider, $action = 'signup')
    {
        try {
            $adapter = Yii::app()->hybridAuth->getHybridAuth()->authenticate(ucfirst(strtolower($provider)));

            $providerProfile = $adapter->getUserProfile();
            $data = array(
                'isNewUser' => false,
                'provider' => $provider,
                'action' => $action
            );

            $socialAuthClass = Yii::app()->getComponent('hauth')->modelMap['SocialAuth']['class'];
            $socialAuthModel = $socialAuthClass::model()->findByAttributes(array(
                $socialAuthClass::model()->fieldProvider => $provider,
                $socialAuthClass::model()->fieldIdentifier => $providerProfile->identifier
            ));
            $userClass = Yii::app()->getComponent('hauth')->modelMap['User']['class'];

            if (null === $socialAuthModel) {
                $socialAuthModel = new $socialAuthClass();
                $userModel = new $userClass();

                $this->onUserFind(new CEvent($this, array(
                    'data' => $data,
                    'userModel' => $userModel,
                    'providerProfile' => $providerProfile
                        )
                ));

                $socialAuthModel->userId = $userModel->id;
                $socialAuthModel->provider = $provider;
                $socialAuthModel->Identifier = $providerProfile->identifier;

                if (!$socialAuthModel->save()) {
                    throw new Exception($this->implodeError($socialAuthModel->getErrors()), 500);
                }
            } else {
                $userModel = $userClass::model()->findByPk($socialAuthModel->userId);
                if ($userModel === null) {
                    throw new Exception('Can not find user model with id - ' . $socialAuthModel->userId, 500);
                }
            }

            $this->onConnect(new CEvent($this, array(
                'data' => $data,
                'userModel' => $userModel,
                'socialAuthModel' => $socialAuthModel,
                'providerProfile' => $providerProfile
                    )
            ));
        } catch (Exception $e) {

            $this->onError(new CEvent($this, array(
                'exeption' => $e,
                    )
            ));
        }
    }

    /**
     * Hybrid auth callback
     * @return boolean
     */
    public function actionCallback()
    {
        try {
            Yii::app()->hybridAuth->getResponse();
        } catch (Hybrid_Exception $e) {
            if ($e->getMessage() === "Oophs. Error!" && Yii::app()->hybridAuth->debugMode === false) {
                return false;
            }
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
        }
    }

    /**
     * @return array|mixed
     */
    public function behaviors()
    {
        return CMap::mergeArray(parent::behaviors(), array(
                    'HybridAuthBehavior' => array(
                        'class' => Yii::app()->getComponent('hauth')->hybridAuthBehavior
                    )
        ));
    }

    /**
     * Implode error to string
     * @param array $errors
     * @return string
     */
    protected function implodeError(array $errors)
    {
        $errorString = '';
        foreach ($errors as $error) {
            $errorString .= implode(', ', $error);
        }
        return $errorString;
    }

    /**
     * Get param from config
     * @param mixed $value
     * @param string $default
     * @return string
     */
    protected function filterParam($value, $default = '')
    {
        if (empty($value) || is_null($value)) {
            return $default;
        }
        return $value;
    }

}
