Hauth extensions
===============

This is extension for simple integrate social signup/signin. For social 
connect used extension hybridauth/hybridauth. In this extension implement yii events 
and evenement (optional. With yiicod/evenement extension you can build powerfull 
events listener for all what you whant).

You can extend HybridAuthBehavior and write what you want on events:
- findUser
- connect
- error

If you want install to extensions folder, insert into composer.json:
--------------------------------------------------------------------
```php
"require": {
    "composer/installers": "1.0.3"
}
```

Config ( This is all config for extensions. Many from this items is optinal)
---------------------------------------------

```php
'components' => array(
    'hauth' => array(
        'modelMap' => array(
            // You can change field names or models
            'SocialAuth' => array(
                'alias' => 'yiicod\hauth\models\SocialAuthModel',
                'class' => 'yiicod\hauth\models\SocialAuthModel',
                'fieldUserId' => 'userId',
                'fieldProvider' => 'provider',
                'fieldIdentifier' => 'identifier',
                'fieldCreateDate' => 'createDate',
            ),
            'User' => array(
                'alias' => 'yiicod\auth\models\UserModel',
                'class' => 'yiicod\auth\models\UserModel',
            )
        ),
        'controllers' => array(
            'controllerMap' => array(
                'default' => array(
                    'socialAuth' => 'yiicod\hauth\controllers\SocialAuthController',
                ),
            ),
            'default' => array(
                'socialAuth' => array(
                    'layout' => '',
                    'filters' => array('accessControl'),
                    'accessRules' => array(
                        array(
                            'allow',
                            'actions' => array('connect'),
                            'users' => array('?')
                        ),
                        array(
                            'allow',
                            'actions' => array('callback'),
                            'users' => array('*')
                        ),
                        array(
                            'deny',
                            'actions' => array(),
                            'users' => array('*')
                        )
                    ),
                ),
            ),
        ),
        'hybridAuthBehavior' => 'yiicod\hauth\controllers\behaviors\HybridAuthBehavior',
        'components' => array(
            //You can set callback and providers what you want to use.
            'hybridAuth' => array(
                'class' => 'yiicod\hauth\components\HybridAuth',
                'callbackRoute' => '/socialAuth/callback',
                'providers' => array(
                ),
                'debugMode' => false,
                'debugFile' => '',
            ),
        ),
    )
)

'preload' => array('hauth')
```

If you want extend SocialAuthController you should set (For exclude duplicate controller):
------------------------------------------------------------------------------------------
```php
'components' => array(
    'hauth' => array(
        'controllers' => array(
            'controllerMap' => array(
                'default' => array(
                    'socialAuth' => null,
                ),
            ),
        )
    )
)
```

Using exampale
--------------
```php

namespace app\modules\auth\controllers\behaviors;

/**
 * SocialAuth behavior with event for controller action
 * @author Orlov Alexey <aaorlov88@gmail.com>
 */

use <some classes>

class HybridAuthBehavior extends HybridAuthBaseBehavior
{

    private function uniqueName($name)
    {
        retrun <uniqueu name>;
    }

    /**
     * User find event
     * @param CEvent $event Object has next params sender -> SocialAuthController, 
     * params -> array('model' => UserModel)
     */
    public function userFind($event)
    {
        parent::userFind($event);

        $data = $event->params['data'];
        $userModel = $event->params['userModel'];
        $providerProfile = $event->params['providerProfile'];

        
        $userAttrs = Yii::app()->db->createCommand()
                ->select('*')
                ->from('User')
                ->where('email=:email', array(':email' => $providerProfile->email))
                ->queryRow();
        if ($userAttrs === false && $data['action'] == 'signup') {
            $userModel->setScenario('signup.social');
            $userModel->userName = $this->uniqueName(preg_replace('/\s/', '', $this->sanitizeTitleWithTranslit($providerProfile->displayName)));
            $userModel->email = empty($providerProfile->email) ? (md5($providerProfile->identifier) . '@' . $data['provider'] . '.com') : $providerProfile->email;
            $userModel->password = $userModel->getOpenUserPassword();

            if ($userModel->save()) {
                $data['isNewUser'] = true;
                $data['identifier'] = $providerProfile->identifier;
            }
        } elseif(is_array($userAttrs)) {
            $userModel->setAttributes($userAttrs);
            $userModel->setPrimaryKey($userAttrs['id']);
        }
        
    }

    /**
     * Connect event
     * @param CEvent $event Object has next params sender -> SocialAuthController, 
     * params -> array('model' => UserModel)
     */
    public function connect($event)
    {
        parent::connect($event);

        $userModel = $event->params['userModel'];
        $identity = new $userIdentity(<login>, <passsword>);
        if (!$identity->authenticate() || !Yii::app()->user->login(identity) {
            if ($event->params['data']['action'] == 'signup') {
                Yii::app()->user->setFlash('error', Yii::t('<key>', 'We are sorry but we can not register you.'));
            } else {
                Yii::app()->user->setFlash('error', Yii::t('<key>', 'We are sorry but we can not login you.'));
            }
        }
        Yii::app()->request->redirect(Yii::app()->createAbsoluteUrl('/user/dasboard'));
        Yii::app()->end();
    }

    public function error($event)
    {
        parent::error($event);
        Yii::app()->user->setFlash('error', Yii::t('<key>', 'We have some problem, try again later.'));
        Yii::app()->request->redirect(Yii::app()->createAbsoluteUrl('/login'));
        Yii::app()->end();
    }

}

```

