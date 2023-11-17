<?php

namespace backend\models;

use common\models\Customer;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CustomerRechargeSearch represents the model behind the search form of `backend\models\CustomerRecharge`.
 */
class CustomerRechargeSearch extends CustomerRecharge
{
    public $page_size = 20;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'institution_id', 'customer_id', 'type', 'status'], 'integer'],
            [['recharge_order_no', 'notes', 'pay_image_path', 'invoice_image_path', 'create_name', 'create_time', 'update_name', 'update_time', 'pay_confirm_name', 'pay_confirm_time'], 'safe'],
            [['amount'], 'number'],
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
        $query = CustomerRecharge::find()->select('cr.*, c.name as customer_name')->alias('cr')->leftJoin(Customer::tableName() . ' c', 'cr.customer_id = c.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'cr.institution_id' => $this->institution_id,
            'cr.customer_id' => $this->customer_id,
        ]);

        $query->andFilterWhere(['like', 'cr.recharge_order_no', $this->recharge_order_no]);
        return $dataProvider;
    }
}
