<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\LogisticCompanyTimeliness;

/**
 * LogisticCompanyTimelinessSearch represents the model behind the search form of `common\models\LogisticCompanyTimeliness`.
 */
class LogisticCompanyTimelinessSearch extends LogisticCompanyTimeliness
{
    public $page_size = 20;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'logistic_id', 'timeliness'], 'integer'],
            [['warehouse_code', 'province', 'city', 'district'], 'safe'],
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
        $query = LogisticCompanyTimeliness::find();

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
            'logistic_id' => $this->logistic_id,
            'timeliness' => $this->timeliness,
        ]);

        $query->andFilterWhere(['like', 'warehouse_code', $this->warehouse_code])
            ->andFilterWhere(['like', 'province', $this->province])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'district', $this->district]);

        return $dataProvider;
    }
}
