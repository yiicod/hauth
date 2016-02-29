<?php

namespace yiicod\hauth\components;

use CApplicationComponent;
use CLogger;
use ErrorException;
use Exception;
use Hybrid_Auth;
use Hybrid_Endpoint;
use Hybrid_Provider_Adapter;
use Yii;

/**
 * Class HybridAuth
 * Component to work with hybridAuth.
 *
 * @author Dmitry Turchanin
 */
class HybridAuth extends CApplicationComponent
{
    /**
     * @var string Route for callbacks
     */
    public $callbackRoute = '';

    /**
     * @var array List of providers
     */
    public $providers = [];

    /**
     * @var bool Flag for switching debug mode
     */
    public $debugMode = false;

    /**
     * @var string File for logging debug information
     */
    public $debugFile = '';

    /**
     * Instance for Hybrid_Auth.
     *
     * @var null
     */
    private $hybridAuth = null;

    public function init()
    {
        parent::init();
        $this->hybridAuth = new Hybrid_Auth($this->getConfig());
    }

    /**
     * Returns the HybridAuth object.
     *
     * @return Hybrid_Auth
     */
    public function getHybridAuth()
    {
        return $this->hybridAuth;
    }

    /**
     * @param type $provider
     *
     * @return Hybrid_Provider_Adapter
     */
    public function getAdapter($provider)
    {
        return $this->getHybridAuth()->getAdapter($provider);
    }

    /**
     * Process response.
     */
    public function getResponse()
    {

        // Overriding the error handler
        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            // We are only interested in one kind of error
            if (strpos($errstr, 'Undefined index') == 0) {
                //We throw an exception that will be catched in the test
                throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
            }

            return false;
        });

        try {
            Hybrid_Endpoint::process();
        } catch (Exception $e) {
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR, 'system.socialAuth');
            // Very important : restoring the previous error handler
            restore_error_handler();
        }

        // Very important : restoring the previous error handler
        restore_error_handler();
    }

    /**
     * Gets profile of the provider.
     *
     * @param $provider
     *
     * @return object
     */
    public function getProviderProfile($provider)
    {
        if (!$this->hybridAuth->isConnectedWith($provider)) {
            return false;
        }
        $adapter = $this->hybridAuth->getAdapter($provider);

        return $adapter->getUserProfile();
    }

    /**
     * Returns array of config.
     *
     * @return array
     */
    private function getConfig()
    {
        $baseUrl = rtrim(str_replace([
            'lang/'.Yii::app()->language,
            'lang/'.Yii::app()->language.'/',
            'lang&'.Yii::app()->language,
            '?lang&'.Yii::app()->language,
                        ], '', Yii::app()->createAbsoluteUrl($this->callbackRoute)), '/');

        return [
            'base_url' => $baseUrl,
            'providers' => $this->providers,
            'debug_mode' => $this->debugMode,
            'debug_file' => $this->debugFile,
        ];
    }
}
