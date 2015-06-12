<?php

namespace yiicod\hauth\components;

use CApplicationComponent;
use Hybrid_Auth;
use Hybrid_Endpoint;
use Hybrid_Provider_Adapter;
use Yii;

/**
 * Class HybridAuth
 * Component to work with hybridAuth
 *
 * @author Dmitry Turchanin
 * @package app\components
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
    public $providers = array();

    /**
     * @var bool Flag for switching debug mode
     */
    public $debugMode = false;

    /**
     * @var string File for logging debug information
     */
    public $debugFile = '';

    /**
     * Instance for Hybrid_Auth
     * @var null
     */
    private $hybridAuth = null;

    public function init()
    {
        parent::init();
        $this->hybridAuth = new Hybrid_Auth($this->getConfig());
    }

    /**
     * Returns the HybridAuth object
     *
     * @return Hybrid_Auth
     */
    public function getHybridAuth()
    {
        return $this->hybridAuth;
    }

    /**
     * 
     * @param type $provider
     * @return Hybrid_Provider_Adapter
     */
    public function getAdapter($provider)
    {
        return $this->getHybridAuth()->getAdapter($provider);
    }

    /**
     * Process response
     */
    public function getResponse()
    {
        Hybrid_Endpoint::process();
    }

    /**
     * Gets profile of the provider
     *
     * @param $provider
     * @return Object
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
     * Returns array of config
     *
     * @return array
     */
    private function getConfig()
    {

        return array(
            'base_url' => rtrim(str_replace(array(
                'lang/' . Yii::app()->language,
                'lang/' . Yii::app()->language . '/',
                'lang&' . Yii::app()->language,
                '?lang&' . Yii::app()->language,
                    ), '', Yii::app()->createAbsoluteUrl($this->callbackRoute)), '/'),
            'providers' => $this->providers,
            'debug_mode' => $this->debugMode,
            'debug_file' => $this->debugFile
        );
    }

}
