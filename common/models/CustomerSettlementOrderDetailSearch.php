<?php

namespace common\models;

use backend\models\Institution;
use common\components\Utility;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CustomerSettlementOrderDetailSearch represents the model behind the search form of `common\models\CustomerSettlementOrderDetail`.
 */
class CustomerSettlementOrderDetailSearch extends CustomerSettlementOrderDetail
{
    public $page_size = 20;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['institution_id', 'customer_id'], 'integer'],
            [['province', 'city', 'district', 'create_time'], 'safe'],
            [['weight', 'need_receipt_amount'], 'number'],
            [['logistic_no',  'settlement_order_no'], 'string', 'max' => 50],

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
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = CustomerSettlementOrderDetail::find()->select('csod.*, c.name as customer_name, i.name as institution_name, o.sender_name, o.sender_phone, o.sender_company, o.sender_address, o.finish_time,o.status as order_status')->alias('csod')->leftJoin(DeliveryOrder::tableName() . ' o', 'csod.logistic_no = o.logistic_no')->leftJoin(Customer::tableName() . ' c', 'csod.customer_id = c.id')->leftJoin(Institution::tableName() . ' i', 'csod.institution_id = i.id');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        $query->andWhere(['csod.settlement_order_no' => $this->settlement_order_no]);
        // grid filtering conditions
        $query->andFilterWhere([
            'csod.institution_id' => $this->institution_id,
            'csod.customer_id' => $this->customer_id,
        ]);
        $query->orderBy('csod.create_time DESC');
//                $result = $dataProvider->getModels();
//print_r($result);exit;
//                echo $query->createCommand()->getRawSql();exit;

        return $dataProvider;
    }


    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchPrint($params)
    {
        $query = CustomerSettlementOrderDetail::find()->select('csod.*, c.name as customer_name, i.name as institution_name, o.sender_name, o.sender_phone, o.sender_company, o.sender_address,o.status as order_status')->alias('csod')->leftJoin(DeliveryOrder::tableName() . ' o', 'csod.logistic_no = o.logistic_no')->leftJoin(Customer::tableName() . ' c', 'csod.customer_id = c.id')->leftJoin(Institution::tableName() . ' i', 'csod.institution_id = i.id');


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);

        $this->load($params);

        $query->andWhere(['csod.settlement_order_no' => $this->settlement_order_no]);
        // grid filtering conditions
        $query->andFilterWhere([
            'csod.institution_id' => $this->institution_id,
            'csod.customer_id' => $this->customer_id,
        ]);
        $query->orderBy('csod.create_time DESC');

//                $result = $dataProvider->getModels();
//print_r($result);exit;
//                echo $query->createCommand()->getRawSql();exit;

        return $dataProvider;
    }


    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function exportData($params)
    {
        $query = CustomerSettlementOrderDetail::find()->select('csod.*, c.name as customer_name, i.name as institution_name, o.sender_name, o.sender_phone, o.sender_company, o.sender_address,o.status as order_status')->alias('csod')->leftJoin(DeliveryOrder::tableName() . ' o', 'csod.logistic_no = o.logistic_no')->leftJoin(Customer::tableName() . ' c', 'csod.customer_id = c.id')->leftJoin(Institution::tableName() . ' i', 'csod.institution_id = i.id');

        $this->load($params);

        $query->andWhere(['csod.settlement_order_no' => $this->settlement_order_no]);
        // grid filtering conditions
        $query->andFilterWhere([
            'csod.institution_id' => $this->institution_id,
            'csod.customer_id' => $this->customer_id,
        ]);
        $query->orderBy('csod.create_time DESC');
//                $result = $dataProvider->getModels();
//print_r($result);exit;
//                echo $query->createCommand()->getRawSql();exit;

        // echo $query->createCommand()->getRawSql();exit;

        $result = $query->asArray()->all();
//        print_r($result);exit;
        $data = [];
        $exportData = [];
        if (!empty($result)) {
            foreach ($result as $value) {
                $data[] = $value['settlement_order_no'];
                $data[] = $value['logistic_no'];
                $data[] = DeliveryOrder::getStatusName($value['order_status']);
                $data[] = $value['institution_name'];
                $data[] = $value['customer_name'];
                $data[] = $value['finish_time'];
                $data[] = $value['sender_name'];
                $data[] = $value['sender_phone'];
                $data[] = $value['sender_company'];
                $data[] = $value['sender_address'];
                $data[] = $value['weight'];
                $data[] = $value['size'];
                $data[] = $value['size_weight'];
                $data[] = $value['need_receipt_amount'];
                $data[] = $value['create_time'];
                $exportData[] = $data;
                unset($data);
            }
        }
        $fileName = '结算明细导出-' . date('YmdHi');
        $header = self::getExportDataHeader();
        Utility::exportData($exportData, $header, $fileName, $fileName);
        exit();
    }

    public static function getExportDataHeader()
    {
        return ['结算单号',
            '快递单号',
            '状态',
            '组织机构名称',
            '客户名称',
            '到达最终状态时间',
            '寄件人姓名',
            '寄件人联系电话',
            '寄件人联系公司',
            '寄件人联系地址',
            '重量千克',
            '体积',
            '体积重量千克',
            '应收金额元',
            '创建时间'
        ];
    }
}
