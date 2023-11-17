<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;

class InitAuthController extends Controller
{
    public function actionInit()
    {
        // 这个是我们上节课添加的authManager组件，组件的调用方式没忘吧？
        $auth = Yii::$app->authManager;
        // 添加 "/blog/index" 权限
        $blogIndex = $auth->createPermission('/userBackend/user-backend/index');
        $blogIndex->description = '用户列表';
        $auth->add($blogIndex);
        // 创建一个角色 '运单管理'，并为该角色分配"/delivery/delivery-order"权限
        $blogManage = $auth->createRole('用户管理');
        $auth->add($blogManage);
        $auth->addChild($blogManage, $blogIndex);
        // 为用户 test1（该用户的uid=1） 分配角色 "博客管理" 权限
        $auth->assign($blogManage, 2); // 1是test1用户的uid

    }

}