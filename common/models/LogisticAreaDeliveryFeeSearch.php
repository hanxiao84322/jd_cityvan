<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\LogisticAreaDeliveryFee;

/**
 * LogisticAreaDeliveryFeeSearch represents the model behind the search form of `common\models\LogisticAreaDeliveryFee`.
 */
class LogisticAreaDeliveryFeeSearch extends LogisticAreaDeliveryFee
{
    public $page_size = 20;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'logistic_id', 'fee_type', 'is_cancel'], 'integer'],
            [['province', 'city', 'district', 'fee_rules', 'create_user', 'create_time', 'update_user', 'update_time'], 'safe'],
            [['invoice_base_price', 'face_order_fee', 'return_fee', 'return_base', 'orders_base_fee', 'under_orders_base_fee', 'follow_price', 'return_rate', 'agent_rate'], 'number'],
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
        $query = LogisticAreaDeliveryFee::find()->select('calf.*, lc.company_name as logistic_name')->alias('calf')->leftJoin(LogisticCompany::tableName() . ' lc', 'calf.logistic_id = lc.id');

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

        // grid filtering conditions
        $query->andFilterWhere([
            'calf.logistic_id'=> $this->logistic_id,
            'calf.city' => $this->city,
            'calf.province' => $this->province,
            'calf.district' => $this->district,
        ]);

        return $dataProvider;
    }
}
