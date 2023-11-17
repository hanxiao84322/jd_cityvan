<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * @package backend\assets
 */
class Select2Asset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/select2.min.css',
    ];
    public $js = [
        'js/select2/select2.full.min.js',
        'js/select2/zh-CN.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
