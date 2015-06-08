<?php

namespace yiicod\hauth\controllers\behaviors;

/**
 * SocialAuth behavior with event for controller action
 * @author Orlov Alexey <aaorlov88@gmail.com>
 */

use CEvent;
use yiicod\hauth\controllers\SocialAuthBase;

class HybridAuthBehavior extends HybridAuthBaseBehavior
{

    /**
     * With this event you can write logic for find exist user model or 
     * create new user from providerProfile data.
     * @param CEvent $event Object has next params sender -> SocialAuthController, 
     * params -> array('userModel' => UserModel, 'providerProfile' => providerProfile, 'data' => array('isNewUser' => boolean, ...))
     */
    public function userFind($event)
    {
        parent::userFind($event);

        $data = $event->params['data'];
        $userModel = $event->params['userModel'];
        $providerProfile = $event->params['providerProfile'];
        //You can set attrs for user or find exists user in db
    }
}
