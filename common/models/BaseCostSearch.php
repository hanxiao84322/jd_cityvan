<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\BaseCost;

/**
 * BaseCostSearch represents the model behind the search form of `common\models\BaseCost`.
 */
class BaseCostSearch extends BaseCost
{
    public $page_size = 20;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'worker_num'], 'integer'],
            [['warehouse', 'month', 'source', 'create_name', 'create_time', 'update_name', 'update_time'], 'safe'],
            [['data_service_fee', 'month_rent', 'worker_fee', 'device_fee'], 'number'],
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
        $query = BaseCost::find();

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
            'data_service_fee' => $this->data_service_fee,
            'month_rent' => $this->month_rent,
            'worker_num' => $this->worker_num,
            'worker_fee' => $this->worker_fee,
            'device_fee' => $this->device_fee,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
        ]);

        $query->andFilterWhere(['like', 'warehouse', $this->warehouse])
            ->andFilterWhere(['like', 'month', $this->month])
            ->andFilterWhere(['like', 'source', $this->source])
            ->andFilterWhere(['like', 'create_name', $this->create_name])
            ->andFilterWhere(['like', 'update_name', $this->update_name]);

        return $dataProvider;
    }
}
