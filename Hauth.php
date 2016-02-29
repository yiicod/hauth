<?php

namespace yiicod\hauth;

use CMap;
use Yii;
use CApplicationComponent;

/**
 * Cms extension settings.
 *
 * @author Orlov Alexey <aaorlov88@gmail.com>
 */
class Hauth extends CApplicationComponent
{
    /**
     * @var array table settings
     */
    public $modelMap = [];

    /**
     * @var array components settings
     */
    public $components = [];

    /**
     * @var array Controllers settings
     */
    public $controllers = [];

    /**
     * @var type
     */
    public $hybridAuthBehavior = null;

    public function init()
    {
        parent::init();
        //Merge main extension config with local extension config
        $config = include dirname(__FILE__).'/config/main.php';
        foreach ($config as $key => $value) {
            if (is_array($value)) {
                //@todo Think about merge array 
                $this->{$key} = CMap::mergeArray($value, $this->{$key});
            } elseif (null === $this->{$key}) {
                $this->{$key} = $value;
            }
        }

        if (!Yii::app() instanceof \CConsoleApplication) {
            //Merge controllers map
            $route = Yii::app()->urlManager->parseUrl(Yii::app()->request);
            $module = substr($route, 0, strpos($route, '/'));

            if (Yii::app()->hasModule($module) && isset($this->controllers['controllerMap'][$module])) {
                Yii::app()->getModule($module)->controllerMap = CMap::mergeArray($this->controllers['controllerMap'][$module], Yii::app()->getModule($module)->controllerMap);
            } elseif (isset($this->controllers['controllerMap']['default'])) {
                Yii::app()->controllerMap = CMap::mergeArray($this->controllers['controllerMap']['default'], Yii::app()->controllerMap);
            }
        }

        Yii::import($this->modelMap['User']['alias']);
        Yii::import($this->modelMap['SocialAuth']['alias']);
        Yii::setPathOfAlias('yiicod', realpath(dirname(__FILE__).'/..'));
        //Set components
        $exists = Yii::app()->getComponents(false);
        foreach ($this->components as $component => $params) {
            if (isset($exists[$component]) && is_object($exists[$component])) {
                unset($this->components[$component]);
            } elseif (isset($exists[$component])) {
                $this->components[$component] = \CMap::mergeArray($params, $exists[$component]);
            }
        }
        Yii::app()->setComponents(
            $this->components, false
        );
    }
}
