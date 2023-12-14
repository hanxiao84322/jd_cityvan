<?php

namespace common\models;

use backend\models\BelongCity;
use backend\models\BelongCityStaff;
use common\components\Utility;

/**
 * This is the model class for table "delivery_order".
 *
 * @property int $id 自增ID
 * @property string|null $logistic_no 邮件单号
 * @property string|null $warehouse_code 库房编码
 * @property string|null $shipping_no 包裹号
 * @property int|null $logistic_id 快递公司ID
 * @property string|null $order_no 订单号
 * @property int|null $sec_logistic_id 第二快递公司ID
 * @property string|null $sec_order_no 第二订单号
 * @property int|null $shipping_num 包裹数量
 * @property float|null $order_weight  订单重量
 * @property float|null $order_weight_rep 订单重量（复查）
 * @property float|null $shipping_weight 包裹重量
 * @property float|null $shipping_weight_rep 包裹重量（复查）
 * @property float|null $post_office_weight 物流重量
 * @property string|null $jd_send_time 京东同步的发货时间
 * @property string|null $send_time 发货时间
 * @property string|null $receive_time 揽收时间-预留
 * @property string|null $package_collection_time 集包时间-预留
 * @property string|null $transporting_time 运输开始时间
 * @property string|null $transported_time 运输结束时间
 * @property string|null $delivering_time 配送中时间
 * @property string|null $allocation_time 分拨时间
 * @property string|null $delivered_time 本人签收时间
 * @property string|null $replace_delivered_time 代签收时间
 * @property string|null $reject_time 拒收时间
 * @property string|null $reject_in_warehouse_time 拒收入库时间
 * @property string|null $finish_time 到达最终状态时间 本人签收时间 代签收时间 拒收入库时间
 * @property string|null $estimate_time 应到时间
 * @property string|null $receiver_name 客户姓名
 * @property string|null $receiver_phone 客户电话
 * @property string|null $receiver_address 客户地址
 * @property string|null $province 省
 * @property string|null $city 市
 * @property string|null $district 区/县
 * @property string|null $towns 乡/镇
 * @property string|null $village 村/社区/居委会
 * @property int|null $status 状态 0 同步 01 发货 02 运输开始 03 运输结束 04 配送中 05  本人签收 06 代签收 07 拒收 08 拒收入库
 * @property string|null $latest_track_info 最后一条物流轨迹信息
 * @property string $latest_track_time 最后一条物流轨迹时间
 * @property string $timeliness 时效（天）
 * @property int|null $is_batch_update 是否批量更新 0 否 1 是
 * @property int|null $is_delay 延误标签 0 否 1 是 妥投时间大于应到时间
 * @property int|null $is_retention 是否超期 0 否 1 是
 * @property int|null $is_serious_retention 是否严重超期 0 否 1 是
 * @property int|null $is_overdue 是否滞留 0 否 1 是
 * @property int|null $is_agent_settle 代理商支付标签 0 否 1 是 代理商结算单为已完成状态
 * @property int|null $is_customer_settle 供应商支付标签 0 否 1 是供应商结算单为已完成状态
 * @property int|null $is_logistic_company_settle 快递公司支付标签 0 否 1 是
 * @property int|null $is_unusual 疑似异常 0 否 1 是
 * @property int $is_deduction 是否存在扣款 0 否 1 是
 * @property string|null $truck_classes_no 卡车班次
 * @property float|null $order_total_price 支付快递公司金额
 * @property float|null $total_price 收取京东金额
 * @property string|null $create_name 创建人用户名
 * @property string|null $create_time 创建时间
 * @property string|null $update_name 更新人用户名
 * @property string|null $update_time 更新时间
 */
class DeliveryOrder extends \yii\db\ActiveRecord
{
    public $customer_name;
    public $institution_name;
    public $logistic_company_name;

    public $total_count;
    public $receive_no_count;
    public $transporting_no_count;
    public $receive_timeout_count;
    public $transporting_timeout_count;

    public $transport_be_time_out;
    public $transport_time_out;
    public $transport_not_found;
    public $delivering_time_out;
    public $delivering_not_found;

    public $retention_two_days;
    public $retention_three_days;
    public $retention_five_days;
    public $retention_seven_days;
    public $retention_ten_days;
    public $retention_more_ten_days;

    public $less_one_day;
    public $date;
    public $one_to_two_day;
    public $two_to_three_day;
    public $three_to_five_day;
    public $five_to_seven_day;
    public $more_seven_day;


    public $create_month;
    public $days;

    const YES = 1;
    const NOT = 0;

//    const AGENT_SETTLED = 1;
//    const AGENT_UNSETTLED = 0;
//    const LOGISTIC_COMPANY_SETTLED = 1;
//    const LOGISTIC_COMPANY_UNSETTLED = 0;

    const STATUS_SYNC = 0;
    const STATUS_SEND = 1;
    const STATUS_TRANSPORTING = 2;
    const STATUS_TRANSPORTED = 3;
    const STATUS_DELIVERING = 4;
    const STATUS_DELIVERED = 5;
    const STATUS_REPLACE_DELIVERED = 6;
    const STATUS_REJECT = 7;
    const STATUS_REJECT_IN_WAREHOUSE = 8;


    public static array $statusList = [
        self::STATUS_SYNC => '同步',
        self::STATUS_SEND => '发货',
        self::STATUS_TRANSPORTING => '运输开始',
        self::STATUS_TRANSPORTED => '运输结束',
        self::STATUS_DELIVERING => '配送中',
        self::STATUS_DELIVERED => '本人签收',
        self::STATUS_REPLACE_DELIVERED => '代签收',
        self::STATUS_REJECT => '拒收',
        self::STATUS_REJECT_IN_WAREHOUSE => '拒收入库',
    ];

    const FULL_STATUS_COMPLETE = 1;
    const FULL_STATUS_BUG = 2;

    public static array $fullStatusList = [
        self::FULL_STATUS_COMPLETE => '信息完整',
        self::FULL_STATUS_BUG => '信息不完整',
    ];

    public static array $yesOrNotList = [
        self::YES => '是',
        self::NOT => '否',
    ];

    public static array $dateTypeList = [
        'device' => '仅有设备有信息',
        'batch_update' => '仅系统有信息',
    ];
    public static array $timeTypeList = [
        'send_time' => '发货时间',
        'receive_time' => '揽收时间',
        'package_collection_time' => '集包时间',
        'transporting_time' => '运输开始时间',
        'transported_time' => '运输结束时间',
        'delivering_time' => '配送开始时间',
        'allocation_time' => '分拨时间',
        'delivered_time' => '妥投时间',
        'reject_time' => '拒收时间',
        'lost_time' => '丢失时间',
        'estimate_time' => '应到时间',
        'create_time' => '创建时间',
    ];
    public $batch_update_file;
    public $is_upload_image;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'delivery_order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['logistic_id', 'sec_logistic_id', 'shipping_num', 'status', 'is_batch_update', 'is_delay', 'is_agent_settle', 'is_customer_settle', 'is_logistic_company_settle', 'is_unusual', 'is_deduction', 'is_retention', 'is_serious_retention', 'is_overdue', 'timeliness'], 'integer'],
            [['order_weight', 'order_weight_rep', 'shipping_weight', 'shipping_weight_rep', 'post_office_weight', 'order_total_price', 'total_price'], 'number'],
            [['jd_send_time', 'send_time', 'receive_time', 'package_collection_time', 'transporting_time', 'transported_time', 'delivering_time', 'allocation_time', 'delivered_time', 'replace_delivered_time', 'reject_time', 'reject_in_warehouse_time', 'finish_time', 'estimate_time', 'latest_track_time', 'create_time', 'update_time'], 'safe'],
            [['logistic_no', 'warehouse_code', 'shipping_no'], 'string', 'max' => 50],
            [['order_no', 'sec_order_no', 'village', 'truck_classes_no', 'create_name', 'update_name'], 'string', 'max' => 20],
            [['receiver_address'], 'string', 'max' => 255],
            [['receiver_name', 'receiver_phone', 'province', 'city', 'district', 'towns'], 'string', 'max' => 100],
            [['latest_track_info'], 'string', 'max' => 800],
            [['logistic_no', 'order_no', 'shipping_no'], 'unique', 'targetAttribute' => ['logistic_no', 'order_no', 'shipping_no']],
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
            'warehouse_code' => '库房编码',
            'shipping_no' => '包裹号',
            'logistic_id' => '快递公司ID',
            'logistic_company_name' => '快递公司',
            'order_no' => '订单号',
            'sec_logistic_id' => '	第二物流名称',
            'sec_order_no' => '第二订单号',
            'shipping_num' => '包裹数量',
            'order_weight' => '订单重量',
            'order_weight_rep' => '订单重量（复查）',
            'shipping_weight' => '包裹重量',
            'shipping_weight_rep' => '包裹重量（复查）',
            'post_office_weight' => '物流重量',
            'send_time' => '发货时间',
            'receive_time' => '揽收时间',
            'package_collection_time' => '集包时间',
            'transporting_time' => '运输开始时间',
            'transported_time' => '运输结束时间',
            'delivering_time' => '配送开始时间',
            'allocation_time' => '分拣时间',
            'delivered_time' => '本人签收时间',
            'replace_delivered_time' => '代签收时间',
            'reject_time' => '拒收时间',
            'reject_in_warehouse_time' => '拒收入库时间',
            'estimate_time' => '应到时间',
            'receiver_name' => '客户姓名',
            'receiver_phone' => '客户电话',
            'receiver_address' => '客户地址',
            'province' => '省',
            'city' => '市',
            'district' => '区/县',
            'towns' => '乡/镇',
            'village' => '村/街道/社区',
            'status' => '状态',
            'latest_track_info' => '最后一条物流轨迹信息',
            'latest_track_time' => '最后一条物流轨迹时间',
            'timeliness' => '时效（天）',
            'is_batch_update' => '是否批量更新',
            'is_delay' => '是否延误',
            'is_retention' => '是否超期',
            'is_serious_retention' => '是否严重超期',
            'is_overdue' => '是否滞留',
            'is_agent_settle' => '代理商是否结算',
            'is_customer_settle' => '客户是否结算',
            'is_logistic_company_settle' => '快递共识是否结算',
            'is_unusual' => '疑似异常',
            'is_deduction' => '是否存在扣款',
            'truck_classes_no' => '卡车批次号',
            'order_total_price' => '支付快递公司金额',
            'total_price' => '收取京东金额',
            'create_name' => '创建人用户名',
            'create_time' => '创建时间',
            'update_name' => '更新人用户名',
            'update_time' => '更新时间',
        ];
    }

    public static function getStatusList()
    {
        return self::$statusList;
    }

    public static function getStatusName($status)
    {
        return isset(self::$statusList[$status]) ? self::$statusList[$status] : '无';
    }

    public static function getYesOrNotName($yesOrNot)
    {
        return isset(self::$yesOrNotList[$yesOrNot]) ? self::$yesOrNotList[$yesOrNot] : '无';
    }

    public static function getSettOption()
    {
        return [
            'customer_settled' => '客户已结算',
            'customer_unsettled' => '客户未结算',
            'agent_settled' => '代理商已结算',
            'agent_unsettled' => '代理商未结算',
            'logistic_company_settled' => '快递公司已结算',
            'logistic_company_unsettled' => '快递公司未结算',
        ];
    }

    /**
     * @param $imagePath
     * @return string
     */
    public static function getLogisticImage($imagePath)
    {
        return '<a href="' . $imagePath . '"><img src="' . $imagePath . '" width="300" height="200"></a>';
    }

    public static function getIsAnalysisOrc($createTime, $orderNo, $analysisNum)
    {
        $time = date('Y-m-d H:i:s', strtotime('-4 day'));
        return $createTime >= '2023-04-19 00:00:00' && $createTime >= $time && $analysisNum < 2 && strlen($orderNo) == 32;
    }

    public static function getSourceByOrderNo($orderNo)
    {
        $orderSource = '';
        $isNeedAnalysisOcr = 0;
        switch ($orderNo) {
            case strlen($orderNo) == 32:
                $orderSource = '拼多多';
                $isNeedAnalysisOcr = 1;
                break;
            case strlen($orderNo) == 19 && (strpos($orderNo, "-") === false && strpos($orderNo, "|") === false):
                $orderSource = '淘宝';
                $isNeedAnalysisOcr = 1;
                break;
            case substr($orderNo, -5, 4) == 'momo':
            case in_array(strlen($orderNo), [6, 8, 14, 15, 16, 17]) && substr($orderNo, 0, 5) != 'HSZHM':
                $orderSource = '菜鸟';
                $isNeedAnalysisOcr = 1;
                break;
            case substr($orderNo, 0, 5) == 'HSZHM':
                $orderSource = '快团团';
                $isNeedAnalysisOcr = 1;
                break;
            case strlen($orderNo) == 5 && is_numeric($orderNo):
            case strlen($orderNo) == 26 && strpos($orderNo, "R") !== false && strpos($orderNo, ".") !== false:
                $orderSource = '天猫';
                $isNeedAnalysisOcr = 1;
                break;
            default:
                break;
        }
        return [
            'orderSource' => $orderSource,
            'isNeedAnalysisOcr' => $isNeedAnalysisOcr
        ];
    }

    public static function batchUpdate($excelData, $username)
    {
        $return = [
            'successCount' => 0,
            'errorCount' => 0,
            'errorList' => '',
        ];
        $errorList = [];
        foreach ($excelData as $line => $item) {
            try {
                $address = Utility::changeToArea($item[11]);
                $logisticNo = (string)$item[0];
                $jdSendTime = $item[1];
                $warehouseCode = (string)$item[2];
                $orderNo = (string)$item[3];
                $shippingNum = intval($item[4]);
                $shippingNo = (string)$item[5];
                $orderWeight = is_float($item[6]) ? $item[6] : (float)$item[6];
                $orderWeightRep = is_float($item[7]) ? $item[7] : (float)$item[7];
                $shippingWeight = is_float($item[8]) ? $item[8] : (float)$item[8];
                $shippingWeightRep = is_float($item[9]) ? $item[9] : (float)$item[9];
                $receiverName = (string)$item[10];
                $receiverAddress = (string)$item[11];
                $province = trim($address['province']);
                $city = trim($address['city']);
                $district = trim($address['district']);
                $receiverPhone = (string)$item[12];
                $postOfficeWeight = is_float($item[13]) ? $item[13] : (float)$item[13];
                $logisticCompany = $item[14];

                if (empty($orderNo)) {
                    continue;
                }
                $isUnusual = 0;
                if (strlen($receiverName) > 40) {
                    $isUnusual = 1;
                }
                $logisticCompanyRes = LogisticCompany::find()->where(['company_name' => $logisticCompany])->asArray()->one();
                if (empty($logisticCompanyRes)) {
                    throw new \Exception('不存在的物流名称:'. $logisticCompany);
                }
                $logisticId = $logisticCompanyRes['id'];

                $warehouseRes = Warehouse::find()->where(['code' => $warehouseCode])->asArray()->one();
                if (empty($warehouseRes)) {
                    throw new \Exception('不存在的仓库编码:' . $warehouseCode);
                }
                //快递公司不存在加一个
//                if (empty($logisticCompanyRes)) {
//                    $logisticCompanyModel = new LogisticCompany();
//                    $logisticCompanyModel->company_name = $logisticCompany;
//                    if (!$logisticCompanyModel->save()) {
//                        throw new \Exception(Utility::arrayToString($logisticCompanyModel->getErrors()));
//                    }
//                    $logisticId = $logisticCompanyModel->attributes['id'];
//                } else {
//                    $logisticId = $logisticCompanyRes['id'];
//                }

                $addressSql = "SELECT `name` FROM `cnarea_2020` WHERE '" . $receiverAddress . "' LIKE CONCAT('%', `name`, '%') and level = 2 and (merger_name like \"%四川%\" or merger_name like \"%青海%\" or merger_name like \"%西藏%\" or merger_name like \"%甘肃%\")";

                $addressResult = \Yii::$app->db->createCommand($addressSql)->queryOne();
                if (!empty($addressResult)) {
                    $district = $addressResult['name'];

                    $city = Cnarea::getParentNameByName($district, Cnarea::LEVEL_THREE);
                    echo $city;exit;
                    $province = Cnarea::getParentNameByName($city, Cnarea::LEVEL_TWO);
                    $timeliness = LogisticCompanyTimeliness::getTimelinessByDeliveryOrderInfo($warehouseCode, $logisticId, $province, $city, $district);
                }
                $deliveryOrderExists = DeliveryOrder::find()->where(['logistic_no' => $logisticNo, 'shipping_no' => $shippingNo])->exists();
                if (!$deliveryOrderExists) {
                    $deliveryOrderModel = new DeliveryOrder();
                    $deliveryOrderModel->create_name = $username;
                    $deliveryOrderModel->create_time = date('Y-m-d H:i:s', time());
                    $deliveryOrderModel->logistic_no = $logisticNo;
                    $deliveryOrderModel->shipping_no = $shippingNo;
                } else {
                    $deliveryOrderModel = DeliveryOrder::findOne(['logistic_no' => $logisticNo, 'shipping_no' => $shippingNo]);
                    $deliveryOrderModel->update_name = $username;
                    $deliveryOrderModel->update_time = date('Y-m-d H:i:s', time());
                }
                $deliveryOrderModel->warehouse_code = $warehouseCode;
                $deliveryOrderModel->jd_send_time = $jdSendTime;
                $deliveryOrderModel->timeliness = !empty($timeliness) ? $timeliness : 0;
                $deliveryOrderModel->order_no = $orderNo;
                $deliveryOrderModel->shipping_num = $shippingNum;
                $deliveryOrderModel->order_weight = $orderWeight;
                $deliveryOrderModel->shipping_weight = $shippingWeight;
                $deliveryOrderModel->order_weight_rep = $orderWeightRep;
                $deliveryOrderModel->shipping_weight_rep = $shippingWeightRep;
                $deliveryOrderModel->post_office_weight = $postOfficeWeight;
                $deliveryOrderModel->receiver_name = $receiverName;
                $deliveryOrderModel->receiver_address = $receiverAddress;
                $deliveryOrderModel->receiver_phone = $receiverPhone;
                $deliveryOrderModel->province = $province;
                $deliveryOrderModel->city = $city;
                $deliveryOrderModel->district = $district;
                $deliveryOrderModel->warehouse_code = $warehouseCode;
                $deliveryOrderModel->logistic_id = $logisticId;
                $deliveryOrderModel->is_unusual = $isUnusual;
                if (!$deliveryOrderModel->save()) {
                    throw new \Exception(Utility::arrayToString($deliveryOrderModel->getErrors()));
                }
                $return['successCount']++;
            } catch (\Exception $e) {
                $return['errorCount']++;
                $errorList[] = '第' . $line . '行失败，' . $e->getMessage();
                $return['errorList'] = $errorList;
            }
        }
        return $return;
    }

    public static function getSizeWeight($size = '', $weight = '')
    {
        $sizeWeight = 0.00;
        if (!empty($size)) {
            $sizeArr = explode('*', $size);
            $sizeCount = $sizeArr[0] * $sizeArr[1] * $sizeArr[2];
            if (!empty($sizeArr)) {
                if ($sizeCount != 0) {
                    if ($weight / ($sizeCount / 1000000) < 300) {
                        $sizeWeight = $sizeCount / 6000;
                    } else {
                        $sizeWeight = $sizeCount / 8000;
                    }
                } else {
                    $sizeWeight = $sizeCount / 8000;
                }

            }
        }
        return round($sizeWeight, 2);
    }

    public static function getLogisticNoByOrderNo($orderNo)
    {
        return self::find()->select('logistic_no')->where(['order_no' => $orderNo])->scalar();
    }

    public static function getLogisticNoByShippingNo($shippingNo)
    {
        return self::find()->select('shipping_no')->where(['shipping_no' => $shippingNo])->scalar();
    }

    public static function getLogisticId($logisticNo)
    {
        return self::find()->select('logistic_id')->where(['logistic_no' => $logisticNo])->scalar();
    }
    public static function getPostOfficeWeight($logisticNo)
    {
        return self::find()->select('post_office_weight')->where(['logistic_no' => $logisticNo])->scalar();
    }

}
