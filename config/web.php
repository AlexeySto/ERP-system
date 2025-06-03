<?php

$params = require __DIR__ . '/params.php';
$db = array_merge(
    require __DIR__ . '/db.php',
    file_exists(__DIR__ . '/db-local.php') ? require __DIR__ . '/db-local.php' : []
);
$menuData = require __DIR__ . '/menuitems.php';

$config = [
    'id' => 'app',
	'name' => 'МП',
    'language' => 'ru-RU',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'rbac'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'modules' => [
        'gridview' =>  [
            'class' => '\kartik\grid\Module'
        ],
        'rbac' => [
            'class' => 'app\modules\rbac\Module',
            'fullAccess' => [
                'roles' => [
                    'Developer',
                    'Owner'
                ],
                'userIds' => [1]
            ]
        ]
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'INhKbr9UxdL3Rahbu71OMTpJFQhGvbbo',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'wazzup' => [
            'class' => 'app\components\wazzup\Wazzup',
            'apiKey' => 'a4a1598099ac43209633eb6ade5ad2d5',
            'channelId' => 'c51d2335-943b-4518-8a58-c97340955d47'
        ],
        'user' => [
            'identityClass' => 'app\models\AuthUser',
            'accessChecker' => 'app\modules\rbac\AccessChecker',
            'enableAutoLogin' => true,
        ],
		'authManager' => [
            'class' => 'yii\rbac\DbManager',
            // uncomment if you want to cache RBAC items hierarchy
            // 'cache' => 'cache',
        ],
		'session' => [
            'timeout' => 3600,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'class' => 'app\components\UrlManager',
            'menuData' => $menuData,
			'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
				'<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
            ],
        ],
        'formatter' => [
            'dateFormat' => 'dd.MM.yyyy',
            'decimalSeparator' => ',',
            'currencyDecimalSeparator' => ',',
            'thousandSeparator' => ' ',
            'currencyCode' => 'RUB',
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
        'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
        'allowedIPs' => ['*'],
    ];
}

return $config;
