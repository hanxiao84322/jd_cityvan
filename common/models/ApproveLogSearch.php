<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ApproveLog;

/**
 * ApproveLogSearch represents the model behind the search form of `common\models\ApproveLog`.
 */
class ApproveLogSearch extends ApproveLog
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'order_type', 'order_id', 'approve_status'], 'integer'],
            [['approve_opinion', 'approve_username', 'approve_name', 'approve_time'], 'safe'],
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
        $query = ApproveLog::find();

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
            'order_type' => $this->order_type,
            'order_id' => $this->order_id,
            'approve_status' => $this->approve_status,
            'approve_time' => $this->approve_time,
        ]);

        $query->andFilterWhere(['like', 'approve_opinion', $this->approve_opinion])
            ->andFilterWhere(['like', 'approve_username', $this->approve_username])
            ->andFilterWhere(['like', 'approve_name', $this->approve_name]);

        //                echo $query->createCommand()->getRawSql();exit;

        return $dataProvider;
    }
}
