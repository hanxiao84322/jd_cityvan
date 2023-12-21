<?php

namespace console\controllers;

use common\components\Utility;
use common\models\LogisticCompanyCheckBill;
use common\models\LogisticCompanyCheckBillDetail;
use yii\console\Controller;

class LogisticCompanyCheckBillController extends Controller
{
    /**
     * ./yii logistic-company-check-bill/run ''
     *
     * @param string $logisticCompanyCheckBillNo
     * @throws \yii\db\Exception
     */
    public function actionRun($logisticCompanyCheckBillNo = '')
    {
        $sql = "SELECT DISTINCT logistic_company_check_bill_no FROM `logistic_company_check_bill_detail` WHERE  1 ";


        if (!empty($logisticCompanyCheckBillNo)) {
            $sql .= " AND logistic_company_check_bill_no =  " . $logisticCompanyCheckBillNo . "  ";
        } else {
            $sql .= " AND logistic_company_check_bill_no like '%TZF%'";
        }
        echo "sql:" . $sql . "\r\n";
        $result = \Yii::$app->db->createCommand($sql)->queryColumn();

        if (empty($result)) {
            echo "Data not found!";
            exit;
        }
        echo "total count:" . count($result) . "\r\n";
        foreach ($result as $tempBillNo) {
            try {

                $logisticCompanyCheckBillSql = "SELECT order_type, logistic_id, warehouse_code, count(logistic_no) as total_count, sum(CASE WHEN (status = 1) THEN 1 ELSE 0 END) as system_total_count, sum(price) as total_price, sum(CASE WHEN (status = 1) THEN price ELSE 0 END) as system_total_price FROM `logistic_company_check_bill_detail` WHERE logistic_company_check_bill_no = '" . $tempBillNo . "' group by order_type, logistic_id, warehouse_code";
                $logisticCompanyCheckBillResult = \Yii::$app->db->createCommand($logisticCompanyCheckBillSql)->queryAll();

                foreach ($logisticCompanyCheckBillResult as $value) {
                    try {
                        $logisticCompanyCheckBillModel = new LogisticCompanyCheckBill();
                        $logisticCompanyCheckBillModel->logistic_company_check_bill_no = LogisticCompanyCheckBill::generateId();
                        $logisticCompanyCheckBillModel->logistic_id = $value['logistic_id'];
                        $logisticCompanyCheckBillModel->warehouse_code = $value['warehouse_code'];;
                        $logisticCompanyCheckBillModel->date = date('Y-m-d', time());
                        $logisticCompanyCheckBillModel->logistic_company_order_num = $value['total_count'];
                        $logisticCompanyCheckBillModel->system_order_num = $value['system_total_count'];
                        $logisticCompanyCheckBillModel->logistic_company_order_price = $value['total_price'];
                        $logisticCompanyCheckBillModel->system_order_price = $value['system_total_price'];
                        $logisticCompanyCheckBillModel->create_username = 'system';
                        $logisticCompanyCheckBillModel->create_time = date('Y-m-d H:i:s', time());
                        $logisticCompanyCheckBillModel->status = LogisticCompanyCheckBill::STATUS_NEW;
                        $logisticCompanyCheckBillModel->type = $value['order_type'];
                        if (!$logisticCompanyCheckBillModel->save()) {
                            throw new \Exception(Utility::arrayToString($logisticCompanyCheckBillModel->getErrors()));
                        }
                        LogisticCompanyCheckBillDetail::updateAll(['logistic_company_check_bill_no' => $logisticCompanyCheckBillModel->logistic_company_check_bill_no], ['logistic_company_check_bill_no' => $tempBillNo]);
                        echo 'logistic_company_check_bill_no：' . $logisticCompanyCheckBillModel->logistic_company_check_bill_no . " check bill insert success\r\n";
                    } catch (\Exception $e) {
                        $errMsg = 'logistic_company_check_bill_no：' . $tempBillNo . " check bill insert error,reason:" . $e->getMessage();
                        echo $errMsg . "\r\n";
                    }
                }
            } catch (\Exception $e) {
                $errMsg = 'logistic_company_check_bill_no：' . $tempBillNo . " check bill insert error,reason:" . $e->getMessage();
                echo $errMsg . "\r\n";
            }
            echo "success\r\n";
        }
        echo "finished\r\n";
    }
}
