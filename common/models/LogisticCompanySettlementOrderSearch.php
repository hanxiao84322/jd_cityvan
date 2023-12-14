<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\LogisticCompanySettlementOrder;

/**
 * LogisticCompanySettlementOrderSearch represents the model behind the search form of `common\models\LogisticCompanySettlementOrder`.
 */
class LogisticCompanySettlementOrderSearch extends LogisticCompanySettlementOrder
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
            [['id', 'logistic_id', 'status'], 'integer'],
            [['settlement_order_no', 'start_time', 'end_time', 'pay_image_path', 'create_name', 'create_time', 'update_name', 'update_time','create_time_start', 'create_time_end', 'logistic_company_check_bill_no', 'warehouse_code'], 'safe'],
            [['need_receipt_amount', 'need_pay_amount', 'adjust_amount', 'need_amount'], 'number'],
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
        $query = LogisticCompanySettlementOrder::find()->select("lcso.*,lc.company_name as logistic_company_name")->alias('lcso')->leftJoin(LogisticCompany::tableName() . ' lc', 'lcso.logistic_id = lc.id');

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

        if (empty($this->create_time_start)) {
            $this->create_time_start = date('Y-m-d 00:00:00', time());
        }
        if (empty($this->create_time_end)) {
            $this->create_time_end = date('Y-m-d 23:59:59', time());
        }
        $query->andWhere(['>=', 'lcso.create_time', $this->create_time_start]);
        $query->andWhere(['<=', 'lcso.create_time', $this->create_time_end]);


        // grid filtering conditions
        $query->andFilterWhere([
            'warehouse_code' => $this->warehouse_code,
            'lcso.logistic_id' => $this->logistic_id,
            'lcso.status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'lcso.settlement_order_no', $this->settlement_order_no]);
        $query->orderBy('lcso.create_time DESC');

//                echo $query->createCommand()->getRawSql();exit;
        return $dataProvider;
    }
}
