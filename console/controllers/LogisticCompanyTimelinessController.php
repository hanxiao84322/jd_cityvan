<?php

namespace console\controllers;

use common\components\Utility;
use common\models\DeliveryOrder;
use common\models\DeliveryOrderOverdueWarning;
use yii\console\Controller;

class LogisticCompanyTimelinessController extends Controller
{
    /**
     *
         * ./yii logistic-company-timeliness/run '2023-12-04' '' '' ''
     *
     * @param string $startTime
     * @param string $endTime
     * @param string $logisticId
     * @param string $logisticNo
     * @throws \yii\db\Exception
     */
    public function actionRun($startTime = '', $endTime = '', $logisticId = '', $logisticNo = '') {
        $sql = "SELECT
    warehouse_code,
    logistic_id,
    SUM(
        CASE WHEN(
            TIMESTAMPDIFF(HOUR, send_time, NOW()) -(timeliness * 24)) < 24 THEN '1' ELSE '0'
        END
    )
 AS less_one_day,
   SUM(
        CASE WHEN(
           48 > TIMESTAMPDIFF(HOUR, send_time, NOW()) -(timeliness * 24)) >= 24 THEN '1' ELSE '0'
        END
    )
 AS one_to_two_day,
   SUM(
        CASE WHEN(
           72 > TIMESTAMPDIFF(HOUR, send_time, NOW()) -(timeliness * 24)) >= 48 THEN '1' ELSE '0'
        END
    )
 AS two_to_three_day,
   SUM(
        CASE WHEN(
           120 > TIMESTAMPDIFF(HOUR, send_time, NOW()) -(timeliness * 24)) >= 72 THEN '1' ELSE '0'
        END
    )
 AS three_to_five_day,
   SUM(
        CASE WHEN(
           148 > TIMESTAMPDIFF(HOUR, send_time, NOW()) -(timeliness * 24)) >= 120 THEN '1' ELSE '0'
        END
    )
 AS five_to_seven_day,
   SUM(
        CASE WHEN(
           TIMESTAMPDIFF(HOUR, send_time, NOW()) -(timeliness * 24)) >= 148 THEN '1' ELSE '0'
        END
    )
 AS more_seven_day
FROM
    `delivery_order` WHERE status NOT IN(" . DeliveryOrder::STATUS_DELIVERED . ", " .DeliveryOrder::STATUS_REPLACE_DELIVERED . ", " .DeliveryOrder::STATUS_REJECT_IN_WAREHOUSE . ") AND timeliness > 0 ";
        if (!empty($logisticNo)) {
            $sql .= " AND logistic_no = '" . $logisticNo . "' ";
        } else {
            if (empty($startTime)) {
                $startTime = date('Y-m-d 00:00:00', strtotime('-1 day'));
            }
            $sql .= "AND send_time >= '" . $startTime . "' ";
            if (empty($endTime)) {
                $endTime = date('Y-m-d 23:59:59', strtotime('-1 day'));
            }
            $sql .= "AND send_time <= '" . $endTime . "' ";

            if (!empty($logisticId)) {
                $sql .= " AND logistic_id = '" . $logisticId . "' ";
            }
        }
        $sql .= " GROUP BY warehouse_code, logistic_id";
        $result = \Yii::$app->db->createCommand($sql)->queryAll();
        if (empty($result)) {
            echo "没有符合的记录。";
            exit;
        }
        echo "有:" . count($result) . "条数据需要处理\r\n";
        foreach ($result as $deliveryOrder) {
            try {
                $currentDay = date('Y-m-d', strtotime($startTime));
                $deliveryOrderOverdueWarningModel = DeliveryOrderOverdueWarning::findOne(['date' => $currentDay, 'warehouse_code' => $deliveryOrder['warehouse_code'], 'logistic_id' => $deliveryOrder['logistic_id']]);
                if (!$deliveryOrderOverdueWarningModel) {
                    $deliveryOrderOverdueWarningModel = new DeliveryOrderOverdueWarning();
                    $deliveryOrderOverdueWarningModel->date = $currentDay;
                    $deliveryOrderOverdueWarningModel->warehouse_code = $deliveryOrder['warehouse_code'];
                    $deliveryOrderOverdueWarningModel->logistic_id = $deliveryOrder['logistic_id'];
                }
                $deliveryOrderOverdueWarningModel->less_one_day = $deliveryOrder['less_one_day'];
                $deliveryOrderOverdueWarningModel->one_to_two_day = $deliveryOrder['one_to_two_day'];
                $deliveryOrderOverdueWarningModel->two_to_three_day = $deliveryOrder['two_to_three_day'];
                $deliveryOrderOverdueWarningModel->three_to_five_day = $deliveryOrder['three_to_five_day'];
                $deliveryOrderOverdueWarningModel->five_to_seven_day = $deliveryOrder['five_to_seven_day'];
                $deliveryOrderOverdueWarningModel->more_seven_day = $deliveryOrder['more_seven_day'];
                if (!$deliveryOrderOverdueWarningModel->save()) {
                    throw new \Exception(Utility::arrayToString($deliveryOrderOverdueWarningModel->getErrors()));
                }
            } catch (\Exception $e) {
                echo $e->getMessage() . "\r\n";
            }
        }
        echo "finished";
    }

}
