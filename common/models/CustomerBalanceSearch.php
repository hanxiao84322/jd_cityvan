<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CustomerBalanceSearch represents the model behind the search form of `common\models\CustomerBalance`.
 */
class CustomerBalanceSearch extends CustomerBalance
{
    public $page_size = 20;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'institution_id', 'customer_id', 'face_orders_num'], 'integer'],
            [['balance'], 'number'],
            [['customer_name','last_recharge_time', 'default_recharge_username', 'last_operation_detail', 'last_recharge_notes', 'update_username', 'update_time'], 'safe'],
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
        $query = CustomerBalance::find()->select('cb.*, c.name as customer_name, c.type as customer_type')->alias('cb')->leftJoin(Customer::tableName() . ' c', 'cb.customer_id = c.id');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'cb.institution_id' => $this->institution_id,
            'update_time' => $this->update_time,
        ]);

        $query->andFilterWhere(['like', 'c.name', $this->customer_name]);
        //        echo $query->createCommand()->getRawSql();exit;

        return $dataProvider;
    }
}
