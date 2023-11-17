<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\LogisticImage;

/**
 * LogisticImageSearch represents the model behind the search form of `common\models\LogisticImage`.
 */
class LogisticImageSearch extends LogisticImage
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['device_id', 'logistic_no', 'image_base64_str', 'create_time'], 'safe'],
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
        $query = LogisticImage::find();

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
            'create_time' => $this->create_time,
        ]);

        $query->andFilterWhere(['like', 'device_id', $this->device_id])
            ->andFilterWhere(['like', 'logistic_no', $this->logistic_no])
            ->andFilterWhere(['like', 'image_base64_str', $this->image_base64_str]);

        return $dataProvider;
    }
}
