<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\DeliveryOrderTask;

/**
 * DeliveryOrderTaskSearch represents the model behind the search form of `common\models\DeliveryOrderTask`.
 */
class DeliveryOrderTaskSearch extends DeliveryOrderTask
{
    public $page_size = 20;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'file_path', 'status'], 'integer'],
            [['result', 'apply_username', 'apply_time', 'start_time', 'end_time'], 'safe'],
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
        $query = DeliveryOrderTask::find();

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
            'file_path' => $this->file_path,
            'status' => $this->status,
            'apply_time' => $this->apply_time,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
        ]);

        $query->andFilterWhere(['like', 'result', $this->result])
            ->andFilterWhere(['like', 'apply_username', $this->apply_username]);
        $query->orderBy('apply_time DESC');

        return $dataProvider;
    }
}
