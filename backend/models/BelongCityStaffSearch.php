<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\BelongCityStaff;

/**
 * BelongCityStaffSearch represents the model behind the search form of `backend\models\BelongCityStaff`.
 */
class BelongCityStaffSearch extends BelongCityStaff
{
    public $page_size = 20;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'belong_city_id'], 'integer'],
            [['code', 'name'], 'safe'],
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
        $query = BelongCityStaff::find()->select('bcs.*, bc.name as belong_city')->alias('bcs')->leftJoin(BelongCity::tableName() . ' bc', 'bcs.belong_city_id = bc.id');

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
            'bcs.belong_city_id' => $this->belong_city_id,
        ]);

        $query->andFilterWhere(['like', 'bcs.code', $this->code])
            ->andFilterWhere(['like', 'bcs.name', $this->name]);

        return $dataProvider;
    }
}
