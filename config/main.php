<?php

return array(
    'modelMap' => array(
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
        'hybridAuth' => array(
            'class' => 'yiicod\hauth\components\HybridAuth',
            'callbackRoute' => '/socialAuth/callback',
            'providers' => array(
            ),
            'debugMode' => false,
            'debugFile' => '',
        ),
    ),
);

