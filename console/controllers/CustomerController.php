<?php

namespace console\controllers;
use backend\models\Institution;
use common\models\Customer;
use yii\console\Controller;

class CustomerController  extends Controller
{
    /**
     * ./yii customer/init
     * @return void
     */
    public function actionInit()
    {
        $institutionList = Institution::find()->asArray()->all();
        foreach ($institutionList as $value) {
            $customerExists = Customer::find()->where(['name' => $value['name']])->exists();
            if (!$customerExists) {
                $customerModel = new Customer();
                $customerModel->name = $value['name'];
                $customerModel->institution_id = $value['parent_id'];
                $customerModel->type = Customer::TYPE_AGENT;
                $customerModel->create_name = 'system';
                $customerModel->create_time = date('Y-m-d H:i:s', time());
                $customerModel->save();
            }
        }

        $customerList = Customer::find()->all();

        foreach ($customerList as $customer) {
            if (!empty($customer->institution_id)) {
                echo '客户名称：'. $customer->name;
                $institution = Institution::findOne(['id' => $customer->institution_id]);
                echo '组织机构名称：'. $institution['name'];
                $parentCustomer = Customer::findOne(['name' => $institution['name']]);
                echo '上级客户ID：'. $parentCustomer->id . "\r\n";
                $customer->parent_customer_id = $parentCustomer->id;
            }
            if ($customer->type == '') {
                $customer->type = Customer::TYPE_SELF;
            }
            $customer->update_name = 'system';
            $customer->update_time = date('Y-m-d H:i:s', time());
            $customer->save();
        }
    }
}
