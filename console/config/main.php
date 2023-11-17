<?php

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'controllerMap' => [
        'fixture' => [
            'class' => \yii\console\controllers\FixtureController::class,
            'namespace' => 'common\fixtures',
          ],
    ],
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
                'db' => [
            'class' => 'yii\db\Connection',
//            'dsn' => 'mysql:host=47.104.206.97;dbname=cityvan', //prod
             'dsn' => 'mysql:host=118.190.204.92;dbname=jd_cityvan', //dev
            'username' => 'root',
//            'password' => '1364338c4b8fc017', //prod
             'password' => '2DD48etx74YSrar2', //dev
            'charset' => 'utf8',
        ],
    ],
    'params' => $params,
];
