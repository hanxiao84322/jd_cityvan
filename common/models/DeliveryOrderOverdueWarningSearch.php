<?php

namespace common\models;

use common\components\Utility;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * DeliveryOrderOverdueWarningSearch represents the model behind the search form of `common\models\DeliveryOrderOverdueWarning`.
 */
class DeliveryOrderOverdueWarningSearch extends DeliveryOrderOverdueWarning
{
    public $page_size = 20;
    public $create_time_start;
    public $create_time_end;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'logistic_id', 'less_one_day', 'one_to_two_day', 'two_to_three_day', 'three_to_five_day', 'five_to_seven_day', 'more_seven_day'], 'integer'],
            [['date', 'warehouse_code', 'create_time_end', 'create_time_start'], 'safe'],
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
     * @param array $dataPower
     *
     * @return ActiveDataProvider
     */
    public function search($params, $dataPower = [])
    {
        $query = DeliveryOrderOverdueWarning::find()->select('doow.*, lc.company_name as logistic_company_name')->alias('doow')->leftJoin(LogisticCompany::tableName() . ' lc', 'doow.logistic_id = lc.id');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }


            if (!empty($dataPower)) {
                if (isset($dataPower['warehouseCodes'])) {
                    $query->andFilterWhere(['in', 'warehouse_code', json_decode(trim($dataPower['warehouseCodes']), true)]);
                } elseif (isset($dataPower['logisticIds'])) {
                    $query->andFilterWhere(['in', 'logistic_id', json_decode($dataPower['logisticIds'], true)]);
                }
            }
        if (empty($this->create_time_start)) {
            $this->create_time_start = date('Y-m-d', strtotime('-1 day'));
        }
        $query->andWhere(['>=', 'date', $this->create_time_start]);

        if (empty($this->create_time_end)) {
            $this->create_time_end = date('Y-m-d', strtotime('-1 day'));
        }
        $query->andWhere(['<=', 'date', $this->create_time_end]);

        // grid filtering conditions
        $query->andFilterWhere([
            'logistic_id' => $this->logistic_id,
            'warehouse_code' => $this->warehouse_code,
        ]);

        return $dataProvider;
    }

    public function searchItems($type, $createTimeStart, $createTimeEnd, $warehouseCode, $logisticId)
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
        $query->andFilterWhere(['>=', 'do.send_time', date('Y-m-d 00:00:00', strtotime($createTimeStart))]);
        $query->andFilterWhere(['<=', 'do.send_time', date('Y-m-d 23:59:59', strtotime($createTimeEnd))]);
        $query->andFilterWhere(['warehouse_code' => $warehouseCode]);
        $query->andFilterWhere(['logistic_id' => $logisticId]);

        $query->andWhere(['not in', 'do.status', [DeliveryOrder::STATUS_DELIVERED, DeliveryOrder::STATUS_REPLACE_DELIVERED]]);
        $query->andWhere(['>', 'do.timeliness', '0']);


        switch ($type) {
            case '1': //运输即将超时
                $query->andWhere('TIMESTAMPDIFF(HOUR, do.send_time, NOW()) -(do.timeliness * 24) < 24');
                break;
            case '2': //超时运输结束
                $query->andWhere('48 > TIMESTAMPDIFF(HOUR, send_time, NOW()) -(timeliness * 24) >= 24');

                break;
            case '3': //无运输结束
                $query->andWhere('72 > TIMESTAMPDIFF(HOUR, send_time, NOW()) -(timeliness * 24) >= 48');
                break;
            case '4': //超时配送中
                $query->andWhere('120 > TIMESTAMPDIFF(HOUR, send_time, NOW()) -(timeliness * 24) >= 72');
                break;
            case '5': //无配送中
                $query->andWhere('148 > TIMESTAMPDIFF(HOUR, send_time, NOW()) -(timeliness * 24) >= 120');
                break;
            case '6': //无配送中
                $query->andWhere('TIMESTAMPDIFF(HOUR, send_time, NOW()) -(timeliness * 24) >= 148');
                break;
            default :
                break;
        }


        return $dataProvider;
    }

    public function searchItemsExport($type, $createTimeStart, $createTimeEnd, $warehouseCode, $logisticId, $typeName)
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
        $query->andFilterWhere(['>=', 'do.send_time', date('Y-m-d 00:00:00', strtotime($createTimeStart))]);
        $query->andFilterWhere(['<=', 'do.send_time', date('Y-m-d 23:59:59', strtotime($createTimeEnd))]);
        $query->andFilterWhere(['warehouse_code' => $warehouseCode]);
        $query->andFilterWhere(['logistic_id' => $logisticId]);

        $query->andWhere(['not in', 'do.status', [DeliveryOrder::STATUS_DELIVERED, DeliveryOrder::STATUS_REPLACE_DELIVERED]]);
        $query->andWhere(['>', 'do.timeliness', '0']);


        switch ($type) {
            case '1': //运输即将超时
                $query->andWhere('TIMESTAMPDIFF(HOUR, do.send_time, NOW()) -(do.timeliness * 24) < 24');
                break;
            case '2': //超时运输结束
                $query->andWhere('48 > TIMESTAMPDIFF(HOUR, send_time, NOW()) -(timeliness * 24) >= 24');

                break;
            case '3': //无运输结束
                $query->andWhere('72 > TIMESTAMPDIFF(HOUR, send_time, NOW()) -(timeliness * 24) >= 48');
                break;
            case '4': //超时配送中
                $query->andWhere('120 > TIMESTAMPDIFF(HOUR, send_time, NOW()) -(timeliness * 24) >= 72');
                break;
            case '5': //无配送中
                $query->andWhere('148 > TIMESTAMPDIFF(HOUR, send_time, NOW()) -(timeliness * 24) >= 120');
                break;
            case '6': //无配送中
                $query->andWhere('TIMESTAMPDIFF(HOUR, send_time, NOW()) -(timeliness * 24) >= 148');
                break;
            default :
                break;
        }


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
        $fileName = '订单信息导出-' . $typeName . '-' . date('YmdHi');
        $header = self::getExportDataHeader();
        Utility::exportData($exportData, $header, $fileName, $fileName);
        exit();
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
}
