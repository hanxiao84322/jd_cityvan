<?php

namespace common\models;

use backend\models\Institution;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CustomerSearch represents the model behind the search form of `common\models\Customer`.
 */
class CustomerSearch extends Customer
{
    public $page_size = 20;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status', 'institution_id'], 'integer'],
            [['sender_name', 'sender_phone', 'sender_company'], 'string'],
            [['name', 'delivery_platform', 'sender_address', 'order_get_type', 'code', 'create_name', 'create_time', 'update_name', 'update_time'], 'safe'],
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
        $query = Customer::find()->select('c.*, i.name as institution_name')->alias('c')->leftJoin(Institution::tableName() . ' i', 'c.institution_id = i.id');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $this->page_size,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'c.status' => $this->status,
            'c.create_time' => $this->create_time,
            'c.update_time' => $this->update_time,
        ]);
        if (isset($this->institution_id) && !empty($this->institution_id)) {
            $query->andFilterWhere(['c.institution_id' => $this->institution_id]);

        }
        $query->andFilterWhere(['like', 'c.name', $this->name])
            ->andFilterWhere(['like', 'c.delivery_platform', $this->delivery_platform])
            ->andFilterWhere(['like', 'c.sender_name', $this->sender_name])
            ->andFilterWhere(['like', 'c.sender_phone', $this->sender_phone])
            ->andFilterWhere(['like', 'c.sender_company', $this->sender_company])
            ->andFilterWhere(['like', 'c.sender_address', $this->sender_address])
            ->andFilterWhere(['like', 'c.order_get_type', $this->order_get_type])
            ->andFilterWhere(['like', 'c.code', $this->code])
            ->andFilterWhere(['like', 'c.create_name', $this->create_name])
            ->andFilterWhere(['like', 'c.update_name', $this->update_name]);
        $query->orderBy('c.`name` is null DESC, c.`code` is null DESC');

        return $dataProvider;
    }
}
