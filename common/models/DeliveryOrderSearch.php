<?php

namespace common\models;

use backend\models\Institution;
use common\components\Utility;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * DeliveryOrderSearch represents the model behind the search form of `common\models\DeliveryOrder`.
 */
class DeliveryOrderSearch extends DeliveryOrder
{
    public int $page_size = 20;
    public $create_time_start;
    public $create_time_end;
    public $time_type;
    public $is_upload_image;
    public $create_month;



    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['logistic_id', 'sec_order_no', 'status', 'is_delay', 'is_deduction', 'is_agent_settle', 'is_customer_settle'], 'integer'],
            [['logistic_no', 'delivery_no', 'order_no', 'sec_order_no', 'send_time', 'receive_time', 'package_collection_time', 'allocation_time', 'delivered_time', 'reject_time', 'estimate_time', 'receiver_name', 'receiver_phone', 'receiver_address', 'province', 'city', 'district', 'towns', 'village', 'latest_track_info', 'truck_classes_no', 'create_name', 'create_time', 'update_name', 'update_time', 'create_time_start', 'create_time_end', 'date_type', 'time_type', 'create_month', 'warehouse_code'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param boolean $isPost
     * @param array $dataPower
     *
     * @return ActiveDataProvider
     */
    public function search($params, $isPost, $dataPower = [])
    {
        $query = DeliveryOrder::find()->select("do.*,lc.company_name as logistic_company_name")->alias('do')->leftJoin(LogisticCompany::tableName() . ' lc', 'do.logistic_id = lc.id');

        if (!$isPost) {
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
            ]);
            $dataProvider->setModels([]);
            return $dataProvider;
        }

        $this->load($params);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $this->page_size,
            ],
        ]);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if (!empty($dataPower)) {
            if (isset($dataPower['warehouseCodes'])) {
                $query->andFilterWhere(['in', 'do.warehouse_code', json_decode(trim($dataPower['warehouseCodes']), true)]);
            } elseif (isset($dataPower['logisticIds'])) {
                $query->andFilterWhere(['in', 'do.logistic_id', json_decode($dataPower['logisticIds'], true)]);
            }
        } else {
            $query->andFilterWhere([
                'do.warehouse_code' => $this->warehouse_code
            ]);
        }

//        $dataProvider = self::getQuery($query, $this->status, $this->order_no, $this->time_type, $this->create_time_start, $this->create_time_end);
        $query->andFilterWhere([
            'do.status' => $this->status,
            'do.logistic_id' => $this->logistic_id,
            'do.receiver_phone' => $this->receiver_phone,
            'do.is_deduction' => $this->is_deduction,

        ]);

        if (!empty($this->logistic_no)) {
            $logisticNos = Utility::getInputData($this->logistic_no);
            $query->andFilterWhere(['in', 'do.logistic_no', $logisticNos]);
        }
        if (!empty($this->order_no)) {
            $orderNos = Utility::getInputData($this->order_no);
            $query->andFilterWhere(['in', 'do.order_no', $orderNos]);
        }
        if (!empty($this->time_type)) {
            if (empty($this->create_time_start)) {
                $this->create_time_start = date('Y-m-d 00:00:00', strtotime('-30 day'));
            }
            if (empty($this->create_time_end)) {
                $this->create_time_end = date('Y-m-d 23:59:59', time());
            }

            switch ($this->time_type) {
                case 'send_time':
                        $query->andWhere(['>=', 'do.send_time', $this->create_time_start]);
                        $query->andWhere(['<=', 'do.send_time', $this->create_time_end]);
                    break;
                case 'receive_time':
                        $query->andWhere(['>=', 'do.receive_time', $this->create_time_start]);
                        $query->andWhere(['<=', 'do.receive_time', $this->create_time_end]);
                    break;
                case 'package_collection_time':
                        $query->andWhere(['>=', 'do.package_collection_time', $this->create_time_start]);
                        $query->andWhere(['<=', 'do.package_collection_time', $this->create_time_end]);
                    break;
                case 'transporting_time':
                        $query->andWhere(['>=', 'do.transporting_time', $this->create_time_start]);
                        $query->andWhere(['<=', 'do.transporting_time', $this->create_time_end]);
                    break;
                case 'transported_time':
                        $query->andWhere(['>=', 'do.transported_time', $this->create_time_start]);
                        $query->andWhere(['<=', 'do.transported_time', $this->create_time_end]);
                    break;
                case 'delivering_time':
                        $query->andWhere(['>=', 'do.delivering_time', $this->create_time_start]);
                        $query->andWhere(['<=', 'do.delivering_time', $this->create_time_end]);
                    break;
                case 'delivered_time':
                        $query->andWhere(['>=', 'do.delivered_time', $this->create_time_start]);
                        $query->andWhere(['<=', 'do.delivered_time', $this->create_time_end]);
                    break;
                case 'allocation_time':
                        $query->andWhere(['>=', 'do.allocation_time', $this->create_time_start]);
                        $query->andWhere(['<=', 'do.allocation_time', $this->create_time_end]);
                    break;
                case 'reject_time':
                        $query->andWhere(['>=', 'do.reject_time', $this->create_time_start]);
                        $query->andWhere(['<=', 'do.reject_time', $this->create_time_end]);
                    break;
                case 'estimate_time':
                        $query->andWhere(['>=', 'do.estimate_time', $this->create_time_start]);
                        $query->andWhere(['<=', 'do.estimate_time', $this->create_time_end]);
                    break;
                case 'create_time':
                        $query->andWhere(['>=', 'do.create_time', $this->create_time_start]);
                        $query->andWhere(['<=', 'do.create_time', $this->create_time_end]);
                    break;
                default :
                    $query->andWhere(['>=', 'do.create_time', $this->create_time_start]);
                    $query->andWhere(['<=', 'do.create_time', $this->create_time_end]);
                    break;
            }
        }
        $query->orderBy('(do.send_time is null), do.send_time DESC, do.create_time DESC');
//                echo $query->createCommand()->getRawSql();exit;

        return $dataProvider;
    }

    public function exportData($params, $dataPower)
    {
        $query = DeliveryOrder::find()->select("do.*, lc.company_name as logistic_company_name")->alias('do')->leftJoin(LogisticCompany::tableName() . ' lc', 'do.logistic_id = lc.id');
        if (!empty($dataPower)) {
            if (isset($dataPower['warehouseCodes'])) {
                $query->andFilterWhere(['in', 'do.warehouse_code', json_decode(trim($dataPower['warehouseCodes']), true)]);
            } elseif (isset($dataPower['logisticIds'])) {
                $query->andFilterWhere(['in', 'do.logistic_id', json_decode($dataPower['logisticIds'], true)]);
            }
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $this->page_size,
            ],
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'do.status' => $this->status,
        ]);
        if (!empty($this->logistic_no)) {
            $logisticNos = Utility::getInputData($this->logistic_no);
            $query->andFilterWhere(['in', 'do.logistic_no', $logisticNos]);
        } else {
            if (empty($this->create_time_start)) {
                $this->create_time_start = date('Y-m-d 00:00:00', strtotime('-30 day'));
            }
            if (empty($this->create_time_end)) {
                $this->create_time_end = date('Y-m-d 23:59:59', time());
            }
            switch ($this->time_type) {
                case 'send_time':
                    $query->andWhere(['>=', 'do.send_time', $this->create_time_start]);
                    $query->andWhere(['<=', 'do.send_time', $this->create_time_end]);
                    break;
                case 'transporting_time':
                    $query->andWhere(['>=', 'do.transporting_time', $this->create_time_start]);
                    $query->andWhere(['<=', 'do.transporting_time', $this->create_time_end]);
                    break;
                case 'transported_time':
                    $query->andWhere(['>=', 'do.transported_time', $this->create_time_start]);
                    $query->andWhere(['<=', 'do.transported_time', $this->create_time_end]);
                    break;
                case 'delivering_time':
                    $query->andWhere(['>=', 'do.delivering_time', $this->create_time_start]);
                    $query->andWhere(['<=', 'do.delivering_time', $this->create_time_end]);
                    break;
                case 'delivered_time':
                    $query->andWhere(['>=', 'do.delivered_time', $this->create_time_start]);
                    $query->andWhere(['<=', 'do.delivered_time', $this->create_time_end]);
                    break;
                case 'allocation_time':
                    $query->andWhere(['>=', 'do.allocation_time', $this->create_time_start]);
                    $query->andWhere(['<=', 'do.allocation_time', $this->create_time_end]);
                    break;
                case 'reject_time':
                    $query->andWhere(['>=', 'do.reject_time', $this->create_time_start]);
                    $query->andWhere(['<=', 'do.reject_time', $this->create_time_end]);
                    break;
                case 'estimate_time':
                    $query->andWhere(['>=', 'do.estimate_time', $this->create_time_start]);
                    $query->andWhere(['<=', 'do.estimate_time', $this->create_time_end]);
                    break;
                case 'create_time':
                    $query->andWhere(['>=', 'do.create_time', $this->create_time_start]);
                    $query->andWhere(['<=', 'do.create_time', $this->create_time_end]);
                    break;
                default :
                    $query->andWhere(['>=', 'do.create_time', $this->create_time_start]);
                    $query->andWhere(['<=', 'do.create_time', $this->create_time_end]);
                    break;
            }
        }
        $query->orderBy('(do.send_time is null), do.send_time DESC, do.create_time DESC');
//         echo $query->createCommand()->getRawSql();exit;

        $result = $query->asArray()->all();
        $data = [];
        $exportData = [];
        if (!empty($result)) {
            foreach ($result as $value) {
                $data[] = $value['logistic_no'];
                $data[] = $value['warehouse_code'];
                $data[] = $value['logistic_company_name'];
                $data[] = $value['order_no'];
                $data[] = $value['shipping_no'];
                $data[] = $value['shipping_num'];
                $data[] = $value['order_weight'];
                $data[] = $value['order_weight_rep'];
                $data[] = $value['shipping_weight'];
                $data[] = $value['shipping_weight_rep'];
                $data[] = $value['post_office_weight'];
                $data[] = $value['receiver_name'];
                $data[] = $value['receiver_phone'];
                $data[] = $value['receiver_address'];
                $data[] = $value['send_time'];
                $data[] = $value['package_collection_time'];
                $data[] = $value['transporting_time'];
                $data[] = $value['transported_time'];
                $data[] = $value['delivering_time'];
                $data[] = $value['delivered_time'];
                $data[] = $value['estimate_time'];
                $data[] = DeliveryOrder::getStatusName($value['status']);
                $data[] = $value['latest_track_info'];
                $data[] = DeliveryOrder::getYesOrNotName($value['is_delay']);
                $data[] = DeliveryOrder::getYesOrNotName($value['is_agent_settle']);
                $data[] = $value['order_total_price'];
                $data[] = $value['total_price'];
                $data[] = $value['create_time'];
                $data[] = $value['update_name'];
                $data[] = $value['update_time'];
                $exportData[] = $data;
                unset($data);
            }
        }
        $fileName = '订单信息导出-' . date('YmdHi');
        $header = self::getExportDataHeader();
        Utility::exportData($exportData, $header, $fileName, $fileName);
        exit();
    }

    public static function getExportHeader()
    {
        return ['订单号*', '业务通知单号', '客户单号', '取件人工号*', '实际件数*', '原单返回', '品名', '实际包装', '实际重量*', '结算方式', '体积(尺寸)', '配载要求', '到达地*', '街道/乡镇', '是否COD', '产品类型*', '客户编码', '寄件人*', '寄件单位', '寄件人地址*', '寄件人电话1*', '寄件人电话2', '专项报价', '收件人*', '收件单位', '收件人地址*', '收件人电话1*', '收件人电话2', '是否签收短信', '投保类型', '投保方式', '声明价值', '包装费', '处理方式', '代收款', '到付款', '重要说明', '联系人（第三方）', '联系电话1（第三方）', '联系电话2（第三方）', '到达地（第三方）', '街道/乡镇（第三方）', '详细地址（第三方）*', '单位（第三方）'];
    }

    public static function getExportDataHeader()
    {
        return ['快递单号',
            '库房号',
            '物流名称',
            '订单号',
            '包裹号',
            '包裹数量',
            '订单重量',
            '订单重量（复查）',
            '包裹重量',
            '包裹重量（复查）',
            '邮局重量',
            '客户姓名',
            '客户电话',
            '客户地址',
            '发货时间',
            '集包时间',
            '运输开始时间',
            '运输结束时间',
            '配送开始时间',
            '妥投时间',
            '应到时间',
            '状态',
            '最后一条物流信息',
            '是否延误',
            '是否结算',
            '订单总金额',
            '订单实付金额',
            '创建时间',
            '更新人用户名',
            '更新时间'];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchSendReceiveTimely($params)
    {
        $query = DeliveryOrder::find()->select("do.warehouse_code, do.logistic_id, lc.company_name as logistic_company_name, COUNT(`do`.`order_no`) as `total_count`, sum(case when (DATEDIFF(NOW(), `do`.`send_time`) > 1 and `do`.`transporting_time` is null) then '1' else '0' end) AS transporting_no_count, sum(case when DATEDIFF(`do`.`transporting_time`, `do`.`send_time`) > 1  and `do`.`transporting_time` is not null then '1' else '0' end) AS transporting_timeout_count")->from(DeliveryOrder::tableName())->alias('do')->leftJoin(LogisticCompany::tableName() . ' lc', 'do.logistic_id = lc.id')->groupBy('do.warehouse_code, do.logistic_id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $this->page_size,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere(['>=', 'do.send_time', date('Y-m-d 00:00:00', strtotime('-7 day'))]);
        $query->andFilterWhere(['<=', 'do.send_time', date('Y-m-d 23:59:59', strtotime('-1 day'))]);

//                echo $query->createCommand()->getRawSql();exit;
//        $result = $query->asArray()->all();
//print_r($result);exit;
        return $dataProvider;
    }

    public function searchSendReceiveTimelyItem($type)
    {
        $query = DeliveryOrder::find()->select("do.*, lc.company_name as logistic_company_name")->alias('do')->leftJoin(LogisticCompany::tableName() . ' lc', 'do.logistic_id = lc.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $this->page_size,
            ],
        ]);


        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere(['>=', 'do.send_time', date('Y-m-d 00:00:00', strtotime('-6 day'))]);
        $query->andFilterWhere(['<=', 'do.send_time', date('Y-m-d 23:59:59', strtotime('-1 day'))]);

        switch ($type) {
            case '1': //无揽收
                $query->andWhere('DATEDIFF(NOW(), `do`.`send_time`) > 1 and `do`.`receive_time` is null');
                break;
            case '2': //无运输
                $query->andWhere('DATEDIFF(NOW(), `do`.`receive_time`) > 1 and `do`.`transporting_time` is null');
                break;
            case '3': //超时揽收
                $query->andWhere('DATEDIFF(`do`.`receive_time`, `do`.`send_time`) > 1');
                break;
            case '4': //超时运输
                $query->andWhere('DATEDIFF(`do`.`transporting_time`, `do`.`receive_time`) > 1');
                break;
            default :
                break;
        }
        $query->orderBy('do.create_time DESC');

//                echo $query->createCommand()->getRawSql();exit;

        return $dataProvider;
    }


    public function searchSendReceiveTimelyItemExport($type, $typeName)
    {
        $query = DeliveryOrder::find()->select("do.*, lc.company_name as logistic_company_name")->alias('do')->leftJoin(LogisticImage::tableName() . ' li', 'do.logistic_no = li.logistic_no');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $this->page_size,
            ],
        ]);


        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere(['>=', 'do.send_time', date('Y-m-d 00:00:00', strtotime('-6 day'))]);
        $query->andFilterWhere(['<=', 'do.send_time', date('Y-m-d 23:59:59', strtotime('-1 day'))]);

        switch ($type) {
            case '1': //无揽收
                $query->andWhere('DATEDIFF(NOW(), `do`.`send_time`) > 1 and `do`.`receive_time` is null');
                break;
            case '2': //无运输
                $query->andWhere('DATEDIFF(NOW(), `do`.`receive_time`) > 1 and `do`.`transporting_time` is null');
                break;
            case '3': //超时揽收
                $query->andWhere('DATEDIFF(`do`.`receive_time`, `do`.`send_time`) > 1');
                break;
            case '4': //超时运输
                $query->andWhere('DATEDIFF(`do`.`transporting_time`, `do`.`receive_time`) > 1');
                break;
            default :
                break;
        }
        $query->orderBy('do.create_time DESC');

//                echo $query->createCommand()->getRawSql();exit;

        $result = $query->asArray()->all();

        $data = [];
        $exportData = [];
        if (!empty($result)) {
            foreach ($result as $value) {
                $data[] = $value['logistic_no'];
                $data[] = $value['customer_name'];
                $data[] = $value['institution_name'];
                $data[] = $value['delivery_no'];
                $data[] = $value['logistic_company_name'];
                $data[] = $value['device_id'];
                $data[] = $value['device_receiver_name'];
                $data[] = $value['device_receiver_phone'];
                $data[] = $value['device_weight'];
                $data[] = $value['receiver_company'];
                $data[] = $value['receiver_address'];
                $data[] = $value['sender_name'];
                $data[] = $value['sender_phone'];
                $data[] = $value['sender_company'];
                $data[] = $value['sender_address'];
                $data[] = $value['taker_name'];
                $data[] = $value['taker_code'];
                $data[] = $value['taker_company'];
                $data[] = $value['sec_logistic_id'];
                $data[] = $value['sec_logistic_no'];
                $data[] = $value['send_time'];
                $data[] = $value['package_collection_time'];
                $data[] = $value['transporting_time'];
                $data[] = $value['transported_time'];
                $data[] = $value['delivering_time'];
                $data[] = $value['delivered_time'];
                $data[] = $value['estimate_time'];
                $data[] = DeliveryOrder::getStatusName($value['status']);
                $data[] = $value['latest_track_info'];
                $data[] = DeliveryOrder::getYesOrNotName($value['is_upload_image']);
                $data[] = DeliveryOrder::getYesOrNotName($value['is_need_analysis_ocr']);
                $data[] = DeliveryOrder::getYesOrNotName($value['is_delay']);
                $data[] = DeliveryOrder::getYesOrNotName($value['is_agent_settle']);
                $data[] = $value['truck_classes_no'];
                $data[] = $value['order_total_price'];
                $data[] = $value['total_price'];
                $data[] = $value['create_time'];
                $data[] = $value['update_name'];
                $data[] = $value['update_time'];
                $exportData[] = $data;
                unset($data);
            }
        }
        $fileName = '订单信息导出-' . $typeName . '-' . date('YmdHi');
        $header = self::getExportDataHeader();
        Utility::exportData($exportData, $header, $fileName, $fileName);
        exit();
    }


    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchTransportWarning($params)
    {
        $query = DeliveryOrder::find()->select("
    COUNT(`do`.`order_no`) AS `total_count`,
    SUM(
        CASE WHEN(
            TIMESTAMPDIFF(HOUR,NOW(), `do`.`transporting_time`) >= 1 AND 
            TIMESTAMPDIFF(HOUR,NOW(), `do`.`transporting_time`) < 12 AND 
            transported_time IS NULL
        ) THEN '1' ELSE '0' END ) AS `transport_be_time_out`,
	SUM(
    	CASE WHEN(
        	DATEDIFF(`do`.`transported_time`,`do`.`transporting_time`) >= 3 AND 
            transported_time IS NOT NULL
    	) THEN '1' ELSE '0' END) AS `transport_time_out`,
	SUM(
    	CASE WHEN(
        DATEDIFF(NOW(), `do`.`transporting_time`) >= 3 AND
        transported_time IS NULL
        ) THEN '1' ELSE '0' END) AS `transport_not_found`,
	SUM(
    	CASE WHEN(
        DATEDIFF(`do`.`delivering_time`,`do`.`transported_time`) >= 1 AND 
            delivering_time IS NOT NULL
    ) THEN '1' ELSE '0' END ) AS `delivering_time_out`,
	SUM(
    	CASE WHEN(
        DATEDIFF(`do`.`delivering_time`,`do`.`transported_time`) >= 2 AND 
            delivering_time IS NULL
    ) THEN '1' ELSE '0' END ) AS `delivering_not_found`
            ")->from(DeliveryOrder::tableName())->alias('do');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $this->page_size,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere(['>=', 'do.send_time', date('Y-m-d 00:00:00', strtotime('-9 day'))]);
        $query->andFilterWhere(['<=', 'do.send_time', date('Y-m-d 23:59:59', strtotime('-1 day'))]);


//                echo $query->createCommand()->getRawSql();exit;
//        $result = $query->asArray()->all();
//print_r($result);exit;
        return $dataProvider;
    }

    public function searchTransportWarningItem($type)
    {
        $query = DeliveryOrder::find()->select("do.*, lc.company_name as logistic_company_name")->alias('do')->leftJoin(LogisticCompany::tableName() . ' lc', 'do.logistic_id = lc.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $this->page_size,
            ],
        ]);


        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere(['>=', 'do.send_time', date('Y-m-d 00:00:00', strtotime('-9 day'))]);
        $query->andFilterWhere(['<=', 'do.send_time', date('Y-m-d 23:59:59', strtotime('-1 day'))]);
        switch ($type) {
            case '1': //运输即将超时
                $query->andWhere(' 
                TIMESTAMPDIFF(HOUR,NOW(), `do`.`transporting_time`) >= 1 AND 
                TIMESTAMPDIFF(HOUR,NOW(), `do`.`transporting_time`) < 12 AND 
                transported_time IS NULL 
                ');
                break;
            case '2': //超时运输结束
                $query->andWhere('
                DATEDIFF(`do`.`transported_time`,`do`.`transporting_time`) >= 3 AND 
                transported_time IS NOT NULL
                ');
                break;
            case '3': //无运输结束
                $query->andWhere('
                DATEDIFF(NOW(), `do`.`transporting_time`) >= 3 AND
                transported_time IS NULL
                ');
                break;
            case 4: //超时配送中
                $query->andWhere('
                DATEDIFF(`do`.`delivering_time`,`do`.`transported_time`) >= 1 AND 
        delivering_time IS NOT NULL
                ');
                break;
            case 5: //无配送中
                $query->andWhere('
                DATEDIFF(`do`.`delivering_time`,`do`.`transported_time`) >= 2 AND 
        delivering_time IS NULL
                ');
                break;
            default :
                break;
        }
        $query->orderBy('do.create_time DESC');

//                echo $query->createCommand()->getRawSql();exit;

        return $dataProvider;
    }


    public function searchTransportWarningItemExport($type, $typeName)
    {
        $query = DeliveryOrder::find()->select("do.*, lc.company_name as logistic_company_name")->alias('do')->leftJoin(LogisticCompany::tableName() . ' lc', 'do.logistic_id = lc.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $this->page_size,
            ],
        ]);


        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere(['>=', 'do.send_time', date('Y-m-d 00:00:00', strtotime('-9 day'))]);
        $query->andFilterWhere(['<=', 'do.send_time', date('Y-m-d 23:59:59', strtotime('-1 day'))]);

        switch ($type) {
            case '1': //运输即将超时
                $query->andWhere(' 
                TIMESTAMPDIFF(HOUR,NOW(), `do`.`transporting_time`) >= 1 AND 
                TIMESTAMPDIFF(HOUR,NOW(), `do`.`transporting_time`) < 12 AND 
                transported_time IS NULL 
                ');
                break;
            case '2': //超时运输结束
                $query->andWhere('
                DATEDIFF(`do`.`transported_time`,`do`.`transporting_time`) >= 3 AND 
                transported_time IS NOT NULL
                ');
                break;
            case '3': //无运输结束
                $query->andWhere('
                DATEDIFF(NOW(), `do`.`delivering_time`) >= 3 AND
                transported_time IS NULL
                ');
                break;
            case '4': //超时配送中
                $query->andWhere('
                DATEDIFF(`do`.`delivering_time`,`do`.`transported_time`) >= 1 AND 
        delivering_time IS NOT NULL
                ');
            case '5': //无配送中
                $query->andWhere('
                DATEDIFF(`do`.`delivering_time`,`do`.`transported_time`) >= 2 AND 
        delivering_time IS NULL
                ');
                break;
            default :
                break;
        }
        $query->orderBy('do.create_time DESC');

//                echo $query->createCommand()->getRawSql();exit;

        $result = $query->asArray()->all();

        $data = [];
        $exportData = [];
        if (!empty($result)) {
            foreach ($result as $value) {
                $data[] = $value['logistic_no'];
                $data[] = $value['customer_name'];
                $data[] = $value['institution_name'];
                $data[] = $value['delivery_no'];
                $data[] = $value['logistic_company_name'];
                $data[] = $value['device_id'];
                $data[] = $value['device_receiver_name'];
                $data[] = $value['device_receiver_phone'];
                $data[] = $value['device_weight'];
                $data[] = $value['receiver_company'];
                $data[] = $value['receiver_address'];
                $data[] = $value['sender_name'];
                $data[] = $value['sender_phone'];
                $data[] = $value['sender_company'];
                $data[] = $value['sender_address'];
                $data[] = $value['taker_name'];
                $data[] = $value['taker_code'];
                $data[] = $value['taker_company'];
                $data[] = $value['sec_logistic_id'];
                $data[] = $value['sec_logistic_no'];
                $data[] = $value['send_time'];
                $data[] = $value['package_collection_time'];
                $data[] = $value['transporting_time'];
                $data[] = $value['transported_time'];
                $data[] = $value['delivering_time'];
                $data[] = $value['delivered_time'];
                $data[] = $value['estimate_time'];
                $data[] = DeliveryOrder::getStatusName($value['status']);
                $data[] = $value['latest_track_info'];
                $data[] = DeliveryOrder::getYesOrNotName($value['is_upload_image']);
                $data[] = DeliveryOrder::getYesOrNotName($value['is_need_analysis_ocr']);
                $data[] = DeliveryOrder::getYesOrNotName($value['is_delay']);
                $data[] = DeliveryOrder::getYesOrNotName($value['is_agent_settle']);
                $data[] = $value['truck_classes_no'];
                $data[] = $value['order_total_price'];
                $data[] = $value['total_price'];
                $data[] = $value['create_time'];
                $data[] = $value['update_name'];
                $data[] = $value['update_time'];
                $exportData[] = $data;
                unset($data);
            }
        }
        $fileName = '订单信息导出-' . $typeName . '-' . date('YmdHi');
        $header = self::getExportDataHeader();
        Utility::exportData($exportData, $header, $fileName, $fileName);
        exit();
    }

    public function searchFinalStatusWarning($params)
    {
        $query = DeliveryOrder::find()->select("do.*, lc.company_name as logistic_company_name")->alias('do')->leftJoin(LogisticCompany::tableName() . ' lc', 'do.logistic_id = lc.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $this->page_size,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andWhere(['>', 'create_time', '']);
        $query->andWhere('DATEDIFF(NOW(), `do`.`send_time`) > 20');
        $query->andWhere('do.status not in (5,6,8)');


        $query->orderBy('do.create_time DESC');

//                echo $query->createCommand()->getRawSql();exit;

        return $dataProvider;
    }

    public function searchOverdue($params)
    {
        $query = DeliveryOrder::find()->select("do.warehouse_code, do.logistic_id, lc.company_name as logistic_company_name,sum((CASE WHEN(
            TIMESTAMPDIFF(HOUR, do.send_time, NOW()) >= 24 AND 
            TIMESTAMPDIFF(HOUR, do.`send_time`, NOW()) < 48 AND 
            do.transporting_time IS NULL
        ) THEN '1' ELSE '0' END) OR (CASE WHEN(
            TIMESTAMPDIFF(HOUR,do.transporting_time, NOW()) >= 24 AND 
            TIMESTAMPDIFF(HOUR,do.`transporting_time`, NOW()) < 48 AND 
            do.transported_time IS NULL
        ) THEN '1' ELSE '0' END) OR (CASE WHEN(
            TIMESTAMPDIFF(HOUR,do.transported_time, NOW()) >= 24 AND 
            TIMESTAMPDIFF(HOUR,do.`transported_time`, NOW()) < 48 AND 
            do.delivering_time IS NULL
        ) THEN '1' ELSE '0' END) OR (CASE WHEN(
            TIMESTAMPDIFF(HOUR,do.delivering_time, NOW()) >= 24 AND 
            TIMESTAMPDIFF(HOUR,do.`delivering_time`, NOW()) < 48 AND 
            (do.replace_delivered_time IS NULL OR do.delivered_time IS NULL)
        ) THEN '1' ELSE '0' END)) AS retention_two_days , 
        sum((CASE WHEN(
            TIMESTAMPDIFF(HOUR,`do`.`send_time`, NOW()) >= 48 AND 
            TIMESTAMPDIFF(HOUR,`do`.send_time, NOW()) < 72 AND 
            do.transporting_time IS NULL
        ) THEN '1' ELSE '0' END) OR (CASE WHEN(
            TIMESTAMPDIFF(HOUR,`do`.transporting_time, NOW()) >= 48 AND 
            TIMESTAMPDIFF(HOUR,`do`.`transporting_time`, NOW()) < 72 AND 
            do.transported_time IS NULL
        ) THEN '1' ELSE '0' END) OR (CASE WHEN(
            TIMESTAMPDIFF(HOUR,`do`.transported_time, NOW()) >= 48 AND 
            TIMESTAMPDIFF(HOUR,`do`.`transported_time`, NOW()) < 72 AND 
            do.delivering_time IS NULL
        ) THEN '1' ELSE '0' END) OR (CASE WHEN(
            TIMESTAMPDIFF(HOUR,`do`.delivering_time, NOW()) >= 48 AND 
            TIMESTAMPDIFF(HOUR,`do`.`delivering_time`, NOW()) < 72 AND 
            (`do`.replace_delivered_time IS NULL OR do.delivered_time IS NULL)
        ) THEN '1' ELSE '0' END)) AS retention_three_days , 
        sum((CASE WHEN(
            TIMESTAMPDIFF(HOUR, (do.send_time), NOW()) >= 72 AND 
            TIMESTAMPDIFF(HOUR, (do.send_time), NOW()) < 120 AND 
            do.transporting_time IS NULL
        ) THEN '1' ELSE '0' END) OR (CASE WHEN(
            TIMESTAMPDIFF(HOUR,(do.transporting_time), NOW()) >= 72 AND 
            TIMESTAMPDIFF(HOUR,(do.transporting_time), NOW()) < 120 AND 
            do.transported_time IS NULL
        ) THEN '1' ELSE '0' END) OR (CASE WHEN(
            TIMESTAMPDIFF(HOUR,(do.transported_time), NOW()) >= 72 AND 
            TIMESTAMPDIFF(HOUR,(do.transported_time), NOW()) < 120 AND 
            do.delivering_time IS NULL
        ) THEN '1' ELSE '0' END) OR (CASE WHEN(
            TIMESTAMPDIFF(HOUR,(do.delivering_time), NOW()) >= 72 AND 
            TIMESTAMPDIFF(HOUR,(do.delivering_time), NOW()) < 120 AND 
            (do.replace_delivered_time IS NULL OR do.delivered_time IS NULL)
        ) THEN '1' ELSE '0' END)) AS retention_five_days ,
        sum((CASE WHEN(
            TIMESTAMPDIFF(HOUR, (do.send_time), NOW()) >= 120 AND 
            TIMESTAMPDIFF(HOUR, (do.send_time), NOW()) < 168 AND 
            do.transporting_time IS NULL
        ) THEN '1' ELSE '0' END) OR (CASE WHEN(
            TIMESTAMPDIFF(HOUR,(do.transporting_time), NOW()) >= 120 AND 
            TIMESTAMPDIFF(HOUR,(do.transporting_time), NOW()) < 168 AND 
            do.transported_time IS NULL
        ) THEN '1' ELSE '0' END) OR (CASE WHEN(
            TIMESTAMPDIFF(HOUR,(do.transported_time), NOW()) >= 120 AND 
            TIMESTAMPDIFF(HOUR,(do.transported_time), NOW()) < 168 AND 
            do.delivering_time IS NULL
        ) THEN '1' ELSE '0' END) OR (CASE WHEN(
            TIMESTAMPDIFF(HOUR,(do.delivering_time), NOW()) >= 120 AND 
            TIMESTAMPDIFF(HOUR,(do.delivering_time), NOW()) < 168 AND 
            (do.replace_delivered_time IS NULL OR do.delivered_time IS NULL)
        ) THEN '1' ELSE '0' END)) AS retention_seven_days ,
        sum((CASE WHEN(
            TIMESTAMPDIFF(HOUR, (do.send_time), NOW()) >= 168 AND 
            TIMESTAMPDIFF(HOUR, (do.send_time), NOW()) < 240 AND 
            do.transporting_time IS NULL
        ) THEN '1' ELSE '0' END) OR (CASE WHEN(
            TIMESTAMPDIFF(HOUR,(do.transporting_time), NOW()) >= 168 AND 
            TIMESTAMPDIFF(HOUR,(do.transporting_time), NOW()) < 240 AND 
            do.transported_time IS NULL
        ) THEN '1' ELSE '0' END) OR (CASE WHEN(
            TIMESTAMPDIFF(HOUR,(do.transported_time), NOW()) >= 168 AND 
            TIMESTAMPDIFF(HOUR,(do.transported_time), NOW()) < 240 AND 
            do.delivering_time IS NULL
        ) THEN '1' ELSE '0' END) OR (CASE WHEN(
            TIMESTAMPDIFF(HOUR,(do.delivering_time), NOW()) >= 168 AND 
            TIMESTAMPDIFF(HOUR,(do.delivering_time), NOW()) < 240 AND 
            (do.replace_delivered_time IS NULL OR do.delivered_time IS NULL)
        ) THEN '1' ELSE '0' END)) AS retention_ten_days ,
        sum((CASE WHEN(
            TIMESTAMPDIFF(HOUR,(do.send_time), NOW()) >= 240 AND 
            do.transporting_time IS NULL
        ) THEN '1' ELSE '0' END) OR (CASE WHEN(
            TIMESTAMPDIFF(HOUR,(do.transporting_time), NOW()) >= 240 AND 
            do.transported_time IS NULL
        ) THEN '1' ELSE '0' END) OR (CASE WHEN(
            TIMESTAMPDIFF(HOUR,(do.transported_time), NOW()) >= 240 AND 
            do.delivering_time IS NULL
        ) THEN '1' ELSE '0' END) OR (CASE WHEN(
            TIMESTAMPDIFF(HOUR,(do.delivering_time), NOW()) >= 240 AND 
            (do.replace_delivered_time IS NULL OR do.delivered_time IS NULL)
        ) THEN '1' ELSE '0' END)) AS retention_more_ten_days")->alias('do')->leftJoin(LogisticCompany::tableName() . ' lc', 'do.logistic_id = lc.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $this->page_size,
            ],
        ]);
        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        if (empty($this->create_month)) {
            $this->create_month = date('Y-m', time());
        }
        $query->andWhere(['>=', 'create_time', date('Y-m-01 00:00:00', strtotime($this->create_month))]);
        $year = date('Y', strtotime($this->create_month));
        $month = date('m', strtotime($this->create_month));

        // 根据年份和月份获取本月的最后一天
        $lastDay = date('t', strtotime($year . '-' . $month . '-01'));
        $query->andWhere(['<=', 'create_time', date('Y-m-' . $lastDay . ' 23:59:59', strtotime($this->create_month))]);

        $query->andWhere('do.status in (1, 2, 3, 4)');

        $query->andFilterWhere([
            'do.warehouse_code' => $this->warehouse_code,
            'do.logistic_id' => $this->logistic_id,
        ]);

        $query->groupBy("do.warehouse_code, do.logistic_id");

        $query->orderBy('do.create_time DESC');

//                echo $query->createCommand()->getRawSql();exit;

        return $dataProvider;
    }

    public function searchFinalStatusWarningExport($params)
    {
        $query = DeliveryOrder::find()->select("do.*,c.name as customer_name,i.name as institution_name, lc.company_name as logistic_company_name, (case when li.logistic_no is null then '0' else '1' end) as is_upload_image")->alias('do')->leftJoin(Customer::tableName() . ' c', 'do.customer_id = c.id')->leftJoin(Institution::tableName() . ' i', 'do.institution_id = i.id')->leftJoin(LogisticCompany::tableName() . ' lc', 'do.logistic_id = lc.id')->leftJoin(LogisticImage::tableName() . ' li', 'do.logistic_no = li.logistic_no');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $this->page_size,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andWhere('DATEDIFF(NOW(), `do`.`send_time`) > 20');
        $query->andWhere('do.status not in (8,9,10)');

        $query->andFilterWhere(['do.institution_id' => $this->institution_id]);

        $query->orderBy('do.create_time DESC');

//                echo $query->createCommand()->getRawSql();exit;

        $result = $query->asArray()->all();

        $data = [];
        $exportData = [];
        if (!empty($result)) {
            foreach ($result as $value) {
                $data[] = $value['logistic_no'];
                $data[] = $value['customer_name'];
                $data[] = $value['institution_name'];
                $data[] = $value['delivery_no'];
                $data[] = $value['logistic_company_name'];
                $data[] = $value['device_id'];
                $data[] = $value['device_receiver_name'];
                $data[] = $value['device_receiver_phone'];
                $data[] = $value['device_weight'];
                $data[] = $value['receiver_company'];
                $data[] = $value['receiver_address'];
                $data[] = $value['sender_name'];
                $data[] = $value['sender_phone'];
                $data[] = $value['sender_company'];
                $data[] = $value['sender_address'];
                $data[] = $value['taker_name'];
                $data[] = $value['taker_code'];
                $data[] = $value['taker_company'];
                $data[] = $value['sec_logistic_id'];
                $data[] = $value['sec_logistic_no'];
                $data[] = $value['send_time'];
                $data[] = $value['package_collection_time'];
                $data[] = $value['transporting_time'];
                $data[] = $value['transported_time'];
                $data[] = $value['delivering_time'];
                $data[] = $value['delivered_time'];
                $data[] = $value['estimate_time'];
                $data[] = DeliveryOrder::getStatusName($value['status']);
                $data[] = $value['latest_track_info'];
                $data[] = DeliveryOrder::getYesOrNotName($value['is_upload_image']);
                $data[] = DeliveryOrder::getYesOrNotName($value['is_need_analysis_ocr']);
                $data[] = DeliveryOrder::getYesOrNotName($value['is_delay']);
                $data[] = DeliveryOrder::getYesOrNotName($value['is_agent_settle']);
                $data[] = $value['truck_classes_no'];
                $data[] = $value['order_total_price'];
                $data[] = $value['total_price'];
                $data[] = $value['create_time'];
                $data[] = $value['update_name'];
                $data[] = $value['update_time'];
                $exportData[] = $data;
                unset($data);
            }
        }
        $fileName = '未达到最终状态预警订单信息导出-' . date('YmdHi');
        $header = self::getExportDataHeader();
        Utility::exportData($exportData, $header, $fileName, $fileName);
        exit();
    }


    public function searchOverdueItems($type, $createMonth, $warehouseCode, $logisticId)
    {
        $query = DeliveryOrder::find()->select("do.*, lc.company_name as logistic_company_name")->alias('do')->leftJoin(LogisticCompany::tableName() . ' lc', 'do.logistic_id = lc.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $this->page_size,
            ],
        ]);


        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andWhere(['>=', 'do.create_time', date('Y-m-01 00:00:00', strtotime($createMonth))]);
        $year = date('Y', strtotime($createMonth));
        $month = date('m', strtotime($createMonth));

        // 根据年份和月份获取本月的最后一天
        $lastDay = date('t', strtotime($year . '-' . $month . '-01'));
        $query->andWhere(['<=', 'do.create_time', date('Y-m-' . $lastDay . ' 23:59:59', strtotime($createMonth))]);
        $query->andWhere('do.status in (1, 2, 3, 4)');

        switch ($type) {
            case '1': //运输即将超时
                $query->andWhere(' 
                (TIMESTAMPDIFF(HOUR, do.send_time, NOW()) >= 24 AND 
                TIMESTAMPDIFF(HOUR, do.`send_time`, NOW()) < 48 AND 
                do.transporting_time IS NULL) OR 
                (TIMESTAMPDIFF(HOUR,do.transporting_time, NOW()) >= 24 AND 
                TIMESTAMPDIFF(HOUR,do.transporting_time, NOW()) < 48 AND 
                do.transported_time IS NULL) OR 
                (TIMESTAMPDIFF(HOUR,do.transported_time, NOW()) >= 24 AND 
                TIMESTAMPDIFF(HOUR,do.transported_time, NOW()) < 48 AND 
                do.delivering_time IS NULL) OR 
                (TIMESTAMPDIFF(HOUR,do.delivering_time, NOW()) >= 24 AND 
                TIMESTAMPDIFF(HOUR,do.delivering_time, NOW()) < 48 AND 
                (do.replace_delivered_time IS NULL OR do.delivered_time IS NULL))  
                ');
                break;
            case '2': //超时运输结束
                $query->andWhere(' 
                (TIMESTAMPDIFF(HOUR, do.send_time, NOW()) >= 48 AND 
                TIMESTAMPDIFF(HOUR, do.`send_time`, NOW()) < 72 AND 
                do.transporting_time IS NULL) OR 
                (TIMESTAMPDIFF(HOUR,do.transporting_time, NOW()) >= 48 AND 
                TIMESTAMPDIFF(HOUR,do.transporting_time, NOW()) < 72 AND 
                do.transported_time IS NULL) OR 
                (TIMESTAMPDIFF(HOUR,do.transported_time, NOW()) >= 48 AND 
                TIMESTAMPDIFF(HOUR,do.transported_time, NOW()) < 72 AND 
                do.delivering_time IS NULL) OR 
                (TIMESTAMPDIFF(HOUR,do.delivering_time, NOW()) >= 48 AND 
                TIMESTAMPDIFF(HOUR,do.delivering_time, NOW()) < 72 AND 
                (do.replace_delivered_time IS NULL OR do.delivered_time IS NULL))  
                ');
                break;
            case '3': //无运输结束
                $query->andWhere(' 
                (TIMESTAMPDIFF(HOUR, do.send_time, NOW()) >= 72 AND 
                TIMESTAMPDIFF(HOUR, do.`send_time`, NOW()) < 120 AND 
                do.transporting_time IS NULL) OR 
                (TIMESTAMPDIFF(HOUR,do.transporting_time, NOW()) >= 72 AND 
                TIMESTAMPDIFF(HOUR,do.transporting_time, NOW()) < 120 AND 
                do.transported_time IS NULL) OR 
                (TIMESTAMPDIFF(HOUR,do.transported_time, NOW()) >= 72 AND 
                TIMESTAMPDIFF(HOUR,do.transported_time, NOW()) < 120 AND 
                do.delivering_time IS NULL) OR 
                (TIMESTAMPDIFF(HOUR,do.delivering_time, NOW()) >= 72 AND 
                TIMESTAMPDIFF(HOUR,do.delivering_time, NOW()) < 120 AND 
                (do.replace_delivered_time IS NULL OR do.delivered_time IS NULL))  
                ');
                break;
            case '4': //无运输结束
                $query->andWhere(' 
                (TIMESTAMPDIFF(HOUR, do.send_time, NOW()) >= 120 AND 
                TIMESTAMPDIFF(HOUR, do.`send_time`, NOW()) < 168 AND 
                do.transporting_time IS NULL) OR 
                (TIMESTAMPDIFF(HOUR,do.transporting_time, NOW()) >= 120 AND 
                TIMESTAMPDIFF(HOUR,do.transporting_time, NOW()) < 168 AND 
                do.transported_time IS NULL) OR 
                (TIMESTAMPDIFF(HOUR,do.transported_time, NOW()) >= 120 AND 
                TIMESTAMPDIFF(HOUR,do.transported_time, NOW()) < 168 AND 
                do.delivering_time IS NULL) OR 
                (TIMESTAMPDIFF(HOUR,do.delivering_time, NOW()) >= 120 AND 
                TIMESTAMPDIFF(HOUR,do.delivering_time, NOW()) < 168 AND 
                (do.replace_delivered_time IS NULL OR do.delivered_time IS NULL))  
                ');
                break;
            case '5': //无运输结束
                $query->andWhere(' 
                (TIMESTAMPDIFF(HOUR, do.send_time, NOW()) >= 168 AND 
                TIMESTAMPDIFF(HOUR, do.`send_time`, NOW()) < 240 AND 
                do.transporting_time IS NULL) OR 
                (TIMESTAMPDIFF(HOUR,do.transporting_time, NOW()) >= 168 AND 
                TIMESTAMPDIFF(HOUR,do.transporting_time, NOW()) < 240 AND 
                do.transported_time IS NULL) OR 
                (TIMESTAMPDIFF(HOUR,do.transported_time, NOW()) >= 168 AND 
                TIMESTAMPDIFF(HOUR,do.transported_time, NOW()) < 240 AND 
                do.delivering_time IS NULL) OR 
                (TIMESTAMPDIFF(HOUR,do.delivering_time, NOW()) >= 168 AND 
                TIMESTAMPDIFF(HOUR,do.delivering_time, NOW()) < 240 AND 
                (do.replace_delivered_time IS NULL OR do.delivered_time IS NULL))  
                ');
                break;
            case 6: //超时配送中
                $query->andWhere(' 
                (TIMESTAMPDIFF(HOUR, do.send_time, NOW()) >= 240 AND 
                do.transporting_time IS NULL) OR 
                (TIMESTAMPDIFF(HOUR,do.transporting_time, NOW()) >= 240 AND 
                do.transported_time IS NULL) OR 
                (TIMESTAMPDIFF(HOUR,do.transported_time, NOW()) >= 240 AND 
                do.delivering_time IS NULL) OR 
                (TIMESTAMPDIFF(HOUR,do.delivering_time, NOW()) >= 240 AND 
                (do.replace_delivered_time IS NULL OR do.delivered_time IS NULL))  
                ');
                break;
            default :
                break;
        }
        $query->andFilterWhere([
            'do.warehouse_code' => $warehouseCode,
            'do.logistic_id' => $logisticId,
        ]);
        $query->orderBy('do.create_time DESC');

//                echo $query->createCommand()->getRawSql();exit;

        return $dataProvider;
    }

}
