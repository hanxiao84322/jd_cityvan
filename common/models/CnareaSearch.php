<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Cnarea;

/**
 * CnareaSearch represents the model behind the search form of `common\models\Cnarea`.
 */
class CnareaSearch extends Cnarea
{
    public $page_size = 20;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'level', 'parent_code', 'area_code', 'zip_code'], 'integer'],
            [['city_code', 'name', 'short_name', 'merger_name', 'pinyin'], 'safe'],
            [['lng', 'lat'], 'number'],
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
        $query = Cnarea::find();

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
            'level' => $this->level,
            'parent_code' => $this->parent_code,
            'area_code' => $this->area_code,
            'zip_code' => $this->zip_code,
            'lng' => $this->lng,
            'lat' => $this->lat,
        ]);

        $query->andFilterWhere(['like', 'city_code', $this->city_code])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'short_name', $this->short_name])
            ->andFilterWhere(['like', 'merger_name', $this->merger_name])
            ->andFilterWhere(['like', 'pinyin', $this->pinyin]);

        return $dataProvider;
    }
}
