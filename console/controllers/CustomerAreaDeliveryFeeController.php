<?php

namespace console\controllers;
use backend\models\CustomerAreaDeliveryFee;
use backend\models\Institution;
use common\components\Utility;
use common\models\Customer;
use yii\console\Controller;

class CustomerAreaDeliveryFeeController  extends Controller
{

    /**
     * ./yii customer-area-delivery-fee/init 4
     *
     * @param string $institutionId
     * @param int $derRun
     * @throws \yii\db\Exception
     */
    public function actionInit($institutionId = '', $derRun = 1)
    {
        $sql = 'SELECT * FROM ' . Customer::tableName() . '  WHERE institution_id = ' . $institutionId . ' AND type =  ' . Customer::TYPE_SELF;
        echo "sql:" . $sql . "\r\n";

        $result = \Yii::$app->db->createCommand($sql)->queryAll();
        if (empty($result)) {
            echo "没有要执行的数据";
            exit;
        }
        $parentInstitutionId = Institution::getParentIdById($institutionId);
        $customerId = Customer::getIdByInstitutionId($institutionId);
        $customerAreaDeliveryFeeSql = "SELECT * FROM " . CustomerAreaDeliveryFee::tableName() . " where institution_id = '" . $parentInstitutionId . "' AND customer_id = '" . $customerId . "'";

        echo "customerAreaDeliveryFeeSql:" . $customerAreaDeliveryFeeSql . "\r\n";

        $customerAreaDeliveryFeeResult = \Yii::$app->db->createCommand($customerAreaDeliveryFeeSql)->queryAll();
        if (empty($customerAreaDeliveryFeeResult)) {
            echo "没有区域运费的数据";
            exit;
        }
        foreach ($result as $item) {
            try {
                foreach ($customerAreaDeliveryFeeResult as $value) {
                    try {
                        $customerAreaDeliveryFeeModel = CustomerAreaDeliveryFee::findOne(['institution_id' => $institutionId, 'customer_id' => $item['id']]);
                        if (!$customerAreaDeliveryFeeModel) {
                            $customerAreaDeliveryFeeModel = new CustomerAreaDeliveryFee();
                            $customerAreaDeliveryFeeModel->institution_id = $institutionId;
                            $customerAreaDeliveryFeeModel->customer_id = $item['id'];
                        }
                        $customerAreaDeliveryFeeModel->fee_type = $value['fee_type'];
                        $customerAreaDeliveryFeeModel->province = $value['province'];
                        $customerAreaDeliveryFeeModel->city = $value['city'];
                        $customerAreaDeliveryFeeModel->district = $value['district'];
                        $customerAreaDeliveryFeeModel->fee_rules = $value['fee_rules'];
                        $customerAreaDeliveryFeeModel->invoice_base_price = $value['invoice_base_price'];
                        $customerAreaDeliveryFeeModel->face_order_fee = $value['face_order_fee'];
                        $customerAreaDeliveryFeeModel->return_fee = $value['return_fee'];
                        $customerAreaDeliveryFeeModel->return_base = $value['return_base'];
                        $customerAreaDeliveryFeeModel->orders_base_fee = $value['orders_base_fee'];
                        $customerAreaDeliveryFeeModel->under_orders_base_fee = $value['under_orders_base_fee'];
                        $customerAreaDeliveryFeeModel->follow_price = $value['follow_price'];
                        $customerAreaDeliveryFeeModel->return_rate = $value['return_rate'];
                        $customerAreaDeliveryFeeModel->agent_rate = $value['agent_rate'];
                        $customerAreaDeliveryFeeModel->is_cancel = $value['is_cancel'];
                        $customerAreaDeliveryFeeModel->create_user = 'system';
                        $customerAreaDeliveryFeeModel->create_time = date('Y-m-d H:i:s', time());
                        if (!$derRun) {
                            if (!$customerAreaDeliveryFeeModel->save()) {
                                throw new \Exception(Utility::arrayToString($customerAreaDeliveryFeeModel->getErrors()));

                            }
                        }
                        print_r($customerAreaDeliveryFeeModel->attributes);

                    } catch (\Exception $e) {
                        echo $e->getMessage() . "\r\n";
                    }
                }
            } catch (\Exception $e) {
                echo $e->getMessage() . "\r\n";
            }

        }



    }
}