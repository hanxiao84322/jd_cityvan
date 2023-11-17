<?php
$db = require __DIR__ . '/db.php';
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'authManager'=>['class'=>'yii\rbac\DbManager'],
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        'helper' => [
            'class' => 'common\components\Helper',
            'property' => '123',
        ],
        // 数据库
        'db' => $db,
    ],
];
