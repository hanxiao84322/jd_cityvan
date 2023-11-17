<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\WorkOrderReply;

/**
 * WorkOrderReplySearch represents the model behind the search form of `common\models\WorkOrderReply`.
 */
class WorkOrderReplySearch extends WorkOrderReply
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['work_order_no', 'reply_content', 'reply_name', 'reply_time'], 'safe'],
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
        $query = WorkOrderReply::find();

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
            'reply_time' => $this->reply_time,
        ]);

        $query->andFilterWhere(['like', 'work_order_no', $this->work_order_no])
            ->andFilterWhere(['like', 'reply_content', $this->reply_content])
            ->andFilterWhere(['like', 'reply_name', $this->reply_name]);

        return $dataProvider;
    }
}
