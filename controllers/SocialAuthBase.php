<?php

namespace yiicod\hauth\controllers;

use CModel;
use Controller;
use Yii;

class SocialAuthBase extends Controller
{

    protected function attachEvent($name, $event)
    {
        if (null === Yii::app()->getComponent('emitter')) {
            return false;
        }
        Yii::app()->emitter->emit($name, array(
            $event
        ));
    }

    /**
     * Action create user
     * @param 
     */
    public function onUserFind($event)
    {      
        $this->attachEvent('yiicod.hauth.controllers.SocialAuthBase.userFind', $event);                
        $this->raiseEvent('onUserFind', $event);        
        
    }

    /**
     * Action error connect
     * @param CModel
     */
    public function onConnect($event)
    {
        $this->attachEvent('yiicod.hauth.controllers.SocialAuthBase.connect', $event);
        $this->raiseEvent('onConnect', $event);
    }

    /**
     * Error
     * @param CModel
     */
    public function onError($event)
    {
        $this->attachEvent('yiicod.hauth.controllers.SocialAuthBase.error', $event);
        $this->raiseEvent('onError', $event);
    }

}
