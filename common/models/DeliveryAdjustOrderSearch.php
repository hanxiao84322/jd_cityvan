<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * DeliveryAdjustOrderSearch represents the model behind the search form of `common\models\DeliveryAdjustOrder`.
 */
class DeliveryAdjustOrderSearch extends DeliveryAdjustOrder
{
    public  $page_size = 20;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'type', 'status'], 'integer'],
            [['logistic_no', 'adjust_order_no', 'note', 'create_time', 'create_name'], 'safe'],
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
        $query = DeliveryAdjustOrder::find();

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
            'adjust_amount' => $this->adjust_amount,
            'type' => $this->type,
            'status' => $this->status,
            'create_time' => $this->create_time,
        ]);

        $query->andFilterWhere(['like', 'logistic_no', $this->logistic_no])
            ->andFilterWhere(['like', 'adjust_order_no', $this->adjust_order_no])
            ->andFilterWhere(['like', 'note', $this->note])
            ->andFilterWhere(['like', 'create_name', $this->create_name]);
        $query->orderBy('id DESC');

        return $dataProvider;
    }

    public function waitApproveSearch($params)
    {
        $query = DeliveryAdjustOrder::find();

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
        $query->andFilterWhere(['in', 'status', [DeliveryAdjustOrder::STATUS_CREATE, DeliveryAdjustOrder::STATUS_FIRST_APPROVED, DeliveryAdjustOrder::STATUS_FIRST_REJECTED, DeliveryAdjustOrder::STATUS_SEC_REJECTED]]);

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'adjust_amount' => $this->adjust_amount,
            'type' => $this->type,
            'status' => $this->status,
            'create_time' => $this->create_time,
        ]);

        $query->andFilterWhere(['like', 'logistic_no', $this->logistic_no])
            ->andFilterWhere(['like', 'adjust_order_no', $this->adjust_order_no])
            ->andFilterWhere(['like', 'note', $this->note])
            ->andFilterWhere(['like', 'create_name', $this->create_name]);
        $query->orderBy('id DESC');

        //                echo $query->createCommand()->getRawSql();exit;


        return $dataProvider;
    }
}
