<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * AdjustOrderSearch represents the model behind the search form of `common\models\AdjustOrder`.
 */
class AdjustOrderSearch extends AdjustOrder
{
    public $page_size = 20;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'customer_id', 'institution_id', 'type', 'status'], 'integer'],
            [['settlement_no', 'note', 'create_time', 'create_name'], 'safe'],
            [['adjust_amount'], 'number'],
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
        $query = AdjustOrder::find()->select('ao.*, c.name as customer_name')->alias('ao')->leftJoin(Customer::tableName() . ' c', 'ao.customer_id = c.id');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'ao.customer_id' => $this->customer_id,
        ]);

        $query->andFilterWhere(['like', 'ao.settlement_no', $this->settlement_no]);

        return $dataProvider;
    }
}
