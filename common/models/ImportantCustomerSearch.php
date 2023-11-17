<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ImportantCustomer;

/**
 * ImportantCustomerSearch represents the model behind the search form of `common\models\ImportantCustomer`.
 */
class ImportantCustomerSearch extends ImportantCustomer
{
    public int $page_size = 20;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'work_order_num', 'level'], 'integer'],
            [['name', 'phone', 'address', 'complaint_type', 'create_time', 'create_name', 'update_time', 'update_name'], 'safe'],
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
        $query = ImportantCustomer::find();

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
            'id' => $this->id,
            'work_order_num' => $this->work_order_num,
            'level' => $this->level,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'complaint_type', $this->complaint_type])
            ->andFilterWhere(['like', 'create_name', $this->create_name])
            ->andFilterWhere(['like', 'update_name', $this->update_name]);

        return $dataProvider;
    }
}
