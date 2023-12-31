<?php

namespace backend\modules\auth\controllers;

use yii\web\Controller;

/**
 * Default controller for the `auth` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->redirect(['/auth/auth-rule/index']);
    }
}
