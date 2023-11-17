<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'name' => '城市先锋运输系统',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    // 配置语言
    'language'=>'zh-CN',
    // 配置时区
    'timeZone'=>'Asia/Chongqing',
    'bootstrap' => ['log'],
    'modules' => [
        'gii' => [
            'class' => 'yii\gii\Module',
            'allowedIPs' => ['*'] // adjust this to your needs
        ],
        'user' => [
            'class' => 'backend\modules\user\Module',
        ],
        'auth' => [
            'class' => 'backend\modules\auth\Module',
        ],
        'userBackend' => [
            'class' => 'backend\modules\userBackend\Module',
        ],
        'delivery' => [
            'class' => 'backend\modules\delivery\Module',
        ],
        'customer' => [
            'class' => 'backend\modules\customer\Module',
        ],
        'warehouse' => [
            'class' => 'backend\modules\warehouse\Module',
        ],
        'institution' => [
            'class' => 'backend\modules\institution\Module',
        ],
        'admin' => [
            'class' => 'mdm\admin\Module',
        ],
        'finance' => [
            'class' => 'backend\modules\finance\Module',
        ],
        'report' => [
            'class' => 'backend\modules\report\Module',
        ],
        'workOrder' => [
            'class' => 'backend\modules\workOrder\Module',
        ],
    ],
    "aliases" => [
        "@mdm/admin" => "@vendor/mdmsoft/yii2-admin",
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'identityClass' => 'backend\models\UserBackend',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'yii\log\DbTarget',
                    'categories' => ['delivery-order-batch-update'],
                    'levels' => ['error', 'warning', 'info'],
                    'logVars' => [],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            //用于表明urlManager是否启用URL美化功能，在Yii1.1中称为path格式URL，
            // Yii2.0中改称美化。
            // 默认不启用。但实际使用中，特别是产品环境，一般都会启用。
            "enablePrettyUrl" => true,
            // 是否启用严格解析，如启用严格解析，要求当前请求应至少匹配1个路由规则，
            // 否则认为是无效路由。
            // 是否在URL中显示入口脚本。是对美化功能的进一步补充。
            "showScriptName" => false,
            // 指定续接在URL后面的一个后缀，如 .html 之类的。仅在 enablePrettyUrl 启用时有效。
            "suffix" => "",
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
        //components数组中加入authManager组件,有PhpManager和DbManager两种方式,
        //PhpManager将权限关系保存在文件里,这里使用的是DbManager方式,将权限关系保存在数据库.
        "authManager" => [
            "class" => 'yii\rbac\DbManager', //这里记得用单引号而不是双引号
            'defaultRoles' => ['guest']
        ],
        'assetManager' => [
            'bundles' => [
                'dmstr\web\AdminLteAsset' => [
                    'skin' => 'skin-red',
                ],
            ],
        ],
    ],
    'as access' => [
        'class' => 'mdm\admin\components\AccessControl',
        'allowActions' => [
            //这里是允许访问的action，不受权限控制
            //controller/action
            'site/*'
        ]
    ],
    'params' => $params,
];
