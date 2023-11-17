<?php

namespace common\models;

use common\components\Utility;
use common\components\ZjsCloud;
use yii\db\Exception;

/**
 * This is the model class for table "delivery_info".
 *
 * @property int $id 自增ID
 * @property string $logistic_no 快递单号
 * @property int $status 状态 0 同步 01 发货 02 运输开始 03 运输结束 04 配送中 05  本人签收 06 代签收 07 拒收 08 拒收入库
 * @property string $content 物流信息
 * @property string $update_time 更新时间
 */
class DeliveryInfo extends \yii\db\ActiveRecord
{

    const STATUS_SEND = 1;
    const STATUS_RECEIVE = 2;
    const STATUS_PACKAGE_COLLECTION = 3;
    const STATUS_TRANSPORTING = 4;
    const STATUS_TRANSPORTED = 5;
    const STATUS_DELIVERING = 6;
    const STATUS_ALLOCATION = 7;
    const STATUS_DELIVERED = 8;
    const STATUS_REJECT = 9;


    public static array $statusList = [
        self::STATUS_SEND => '已发货',
        self::STATUS_RECEIVE => '已揽收',
        self::STATUS_TRANSPORTING => '运输中',
        self::STATUS_TRANSPORTED => '运输送达',
        self::STATUS_DELIVERING => '配送中',
        self::STATUS_PACKAGE_COLLECTION => '已集包',
        self::STATUS_ALLOCATION => '已分拨',
        self::STATUS_DELIVERED => '已妥投',
        self::STATUS_REJECT => '已拒收',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'delivery_info';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['logistic_no', 'status', 'content'], 'required'],
            [['status'], 'integer'],
            [['update_time'], 'safe'],
            [['logistic_no'], 'string', 'max' => 20],
            [['content'], 'string', 'max' => 500],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'logistic_no' => '快递单号',
            'status' => 'Status',
            'content' => 'Content',
            'update_time' => 'Update Time',
        ];
    }

    public static function create($data = [])
    {
        $return = [
            'success' => false,
            'msg' => '',
        ];
        //保存物流信息
        $deliveryInfoModel = new self();
        $deliveryInfoModel->logistic_no = $data['logistic_no'];
        $deliveryInfoModel->status = $data['status'];
        $deliveryInfoModel->content = $data['content'];;
        if (!$deliveryInfoModel->save()) {
            $return['msg'] =  Utility::arrayToString($deliveryInfoModel->getErrors());
        }
        $return['success'] = true;
        return $return;
    }

    public static function getStepsByLogisticNo($logisticNo)
    {
        $stepsHtml = '<p>订单号' . $logisticNo . '的物流轨迹</p>';
$stepsHtml .= '<table style="margin: 10px; padding: 10px; width: 100%; padding: 10px 10px 10px 10px;">';
        $stepsHtml .= '<tr>';
        $stepsHtml .= '<th>操作时间</th><th>轨迹</th>';
        $stepsHtml .= '</tr>';
        $res = self::find()->where(['logistic_no' => $logisticNo])->asArray()->orderBy('update_time desc')->all();
        if (!empty($res)) {
            foreach ($res as $val) {
                $stepsHtml .= '<tr>';
                $stepsHtml .= '<td>' . $val['update_time'] . '</td><td>' . $val['content'] . '</td>';
                $stepsHtml .= '</tr>';
            }
        }
        $stepsHtml .= '</table>';
        return $stepsHtml;
    }

    public static function getContentAndTimeByLogisticNoAndStatus($logisticNo, $orderNo, $status)
    {
        $return = [
            'success' => false,
            'msg' => '',
            'res' => [
                'status' => '',
                'content' => '',
                'time' => '',
                'secLogisticCompany' => '',
                'secLogisticNo' => ''
            ]
        ];
        try {
            $deliveryInfoRes = ZjsCloud::getDeliveryInfo($logisticNo);
            if (!$deliveryInfoRes['success']) {
                throw new Exception("快递单号：" . $logisticNo . "获取物流轨迹失败，原因：" . $deliveryInfoRes['msg']);
            }
            $deliveryInfoData = json_decode($deliveryInfoRes['data'], true);
            if ($deliveryInfoData['description'] !== '成功!') {
                throw new Exception("快递单号：" . $logisticNo . "解析物流轨迹失败，原因：" . $deliveryInfoData['description']);
            }
            if ($deliveryInfoData['clientFlag'] != ZjsCloud::$clientFlag) {
                throw new Exception("快递单号：" . $logisticNo . "解析物流轨迹失败,原因：客户标识错误");
            }
            if (isset($deliveryInfoData['orders'][0]) && !empty($deliveryInfoData['orders'][0])) {
                $deliveryInfo = $deliveryInfoData['orders'][0];
                if ($logisticNo != $deliveryInfo['mailNo'] || $orderNo != $deliveryInfo['orderNo']) {
                    throw new Exception("快递单号：" . $logisticNo . "解析物流轨迹失败，原因：快递单号或订单号不一致，解析出快递单号为：" . $deliveryInfo['mailNo']);
                }
                if (isset($deliveryInfo['steps']) && !empty($deliveryInfo['steps'])) {
                    $deliverySteps = $deliveryInfo['steps'];
                    foreach ($deliverySteps as $deliveryStep) {
                        try {
                            $operationDescribe = $deliveryStep['operationDescribe'];
                            $operationTime = $deliveryStep['operationTime'];
                            if (strpos($operationDescribe, '已取件') !== false && $status == DeliveryOrder::STATUS_RECEIVE) {
                                $return['res']['status'] = DeliveryOrder::STATUS_RECEIVE;
                                $return['res']['time'] = $operationTime;
                                $return['res']['content'] = $operationDescribe;
                            } elseif (($operationDescribe != '您的快件离开【乌鲁木齐转运中心】，已发往【新疆直营市场部公司】' && (strpos($operationDescribe, '离开') !== false || (strpos($operationDescribe, '到达') !== false && (strpos($operationDescribe, '[新疆') === false) && (strpos($operationDescribe, '【新疆') === false) && (strpos($operationDescribe, '[乌鲁木齐') === false) && (strpos($operationDescribe, '【乌鲁木齐') === false)))) && $status == DeliveryOrder::STATUS_TRANSPORTING) {
                                $return['res']['status'] = DeliveryOrder::STATUS_TRANSPORTING;
                                $return['res']['time'] = $operationTime;
                                $return['res']['content'] = $operationDescribe;
                            } elseif (((strpos($operationDescribe, '到达[新疆') !== false) || (strpos($operationDescribe, '到达【新疆') !== false) || (strpos($operationDescribe, '到达[乌鲁木齐') !== false) || (strpos($operationDescribe, '到达【乌鲁木齐') !== false)) && $status == DeliveryOrder::STATUS_TRANSPORTED) {
                                $return['res']['status'] = DeliveryOrder::STATUS_TRANSPORTED;
                                $return['res']['time'] = $operationTime;
                                $return['res']['content'] = $operationDescribe;
                            } elseif (strpos($operationDescribe, '货物已转发') !== false && $status == DeliveryOrder::STATUS_DELIVERING) {
                                $pattern = '/\[(.*?)\]/';
                                preg_match_all($pattern, $operationDescribe, $matches);
                                if (!empty($matches[1])) {
                                    if (isset($matches[1][0]) && !empty($matches[1][0])) {
                                        $return['res']['secLogisticCompany'] = $matches[1][0];
                                    }
                                    if (isset($matches[1][1]) && !empty($matches[1][1])) {
                                        $return['res']['secLogisticNo'] = $matches[1][1];
                                    }
                                }
                                $return['res']['status'] = DeliveryOrder::STATUS_DELIVERING;
                                $return['res']['time'] = $operationTime;
                                $return['res']['content'] = $operationDescribe;
                            } elseif (strpos($operationDescribe, '签收') !== false && $status == DeliveryOrder::STATUS_DELIVERED) {
                                $return['res']['status'] = DeliveryOrder::STATUS_DELIVERED;
                                $return['res']['time'] = $operationTime;
                                $return['res']['content'] = $operationDescribe;
                            } elseif ((strpos($operationDescribe, '拒收') !== false || strpos($operationDescribe, '您的快件离开【乌鲁木齐转运中心】，已发往【新疆直营市场部公司】') !== false)  && $status == DeliveryOrder::STATUS_REJECT) {
                                $return['res']['status'] = DeliveryOrder::STATUS_REJECT;
                                $return['res']['time'] = $operationTime;
                                $return['res']['content'] = $operationDescribe;
                            } elseif (strpos($operationDescribe, '丢失') !== false && $status == DeliveryOrder::STATUS_LOST) {
                                $return['res']['status'] = DeliveryOrder::STATUS_LOST;
                                $return['res']['time'] = $operationTime;
                                $return['res']['content'] = $operationDescribe;
                            }
                        } catch (Exception $exception) {
                            $return['msg'] = "快递单号：" . $logisticNo . "更新运单信息失败，原因：" . $exception->getMessage();
                        }
                    }
                }
            }
        } catch (Exception $e) {
            $return['msg'] = "快递单号：" . $logisticNo . "更新运单信息失败，原因：" . $e->getMessage();
        }
        
        $return['success'] = true;
        return $return;
    }
}
