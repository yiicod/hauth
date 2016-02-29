<?php

return [
    'modelMap' => [
        'SocialAuth' => [
            'alias' => 'yiicod\hauth\models\SocialAuthModel',
            'class' => 'yiicod\hauth\models\SocialAuthModel',
            'fieldUserId' => 'userId',
            'fieldProvider' => 'provider',
            'fieldIdentifier' => 'identifier',
            'fieldCreateDate' => 'createDate',
        ],
        'User' => [
            'alias' => 'yiicod\auth\models\UserModel',
            'class' => 'yiicod\auth\models\UserModel',
        ],
    ],
    'controllers' => [
        'controllerMap' => [
            'default' => [
                'socialAuth' => 'yiicod\hauth\controllers\SocialAuthController',
            ],
        ],
        'default' => [
            'socialAuth' => [
                'layout' => '',
                'filters' => ['accessControl'],
                'accessRules' => [
                    [
                        'allow',
                        'actions' => ['connect'],
                        'users' => ['?'],
                    ],
                    [
                        'allow',
                        'actions' => ['callback'],
                        'users' => ['*'],
                    ],
                    [
                        'deny',
                        'actions' => [],
                        'users' => ['*'],
                    ],
                ],
            ],
        ],
    ],
    'hybridAuthBehavior' => 'yiicod\hauth\controllers\behaviors\HybridAuthBehavior',
    'components' => [
        'hybridAuth' => [
            'class' => 'yiicod\hauth\components\HybridAuth',
            'callbackRoute' => '/socialAuth/callback',
            'providers' => [
            ],
            'debugMode' => false,
            'debugFile' => '',
        ],
    ],
];
