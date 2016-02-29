<?php

namespace yiicod\hauth\controllers\behaviors;

/*
 * Base hauth behavior, with all declarate events
 * @author Orlov Alexey <aaorlov@gmail.com>
 */

use CBehavior;
use CEvent;
use CLogger;
use Yii;

class HybridAuthBaseBehavior extends CBehavior
{
    /**
     * Declares events and the corresponding event handler methods.
     * If you override this method, make sure you merge the parent result to the return value.
     *
     * @return array events (array keys) and the corresponding event handler methods (array values).
     *
     * @see CBehavior::events
     */
    public function events()
    {
        return array_merge(parent::events(), [
            'onUserFind' => 'userFind',
            'onConnect' => 'connect',
            'onError' => 'error',
        ]);
    }

    /**
     * Action on try user find.
     *
     * @param 
     */
    public function userFind($event)
    {
    }

    /**
     * Action social connect.
     *
     * @param CEvent
     */
    public function connect($event)
    {
    }

    /**
     * Action on some error.
     *
     * @param CEvent
     */
    public function error($event)
    {
        Yii::log($event->params['exeption']->getMessage(), CLogger::LEVEL_ERROR, 'system.socialAuth');
    }
}
