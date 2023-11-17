<?php

namespace common\models;

use backend\models\BelongCity;
use backend\models\Institution;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * DeviceSearch represents the model behind the search form of `common\models\Device`.
 */
class DeviceSearch extends Device
{
    public $page_size = 20;
    public $is_sun = 0;
    public $is_parent = 0;
    public $institution_id = 0;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'is_parent'], 'integer'],
            [['code', 'belong_city_id', 'direction'], 'safe'],
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
        $query = Device::find()->select('d.*, b.name as belong_city_name')->alias('d')->leftJoin(BelongCity::tableName() . ' b', 'd.belong_city_id = b.id');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $this->page_size,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following l    ine if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'd.id' => $this->id,
            'd.belong_city_id' => $this->belong_city_id,
        ]);
        if ($this->is_sun) {
            $belongCityIdList = Institution::getBelongCityIdListById($this->institution_id);
            $query->andFilterWhere(['in', 'd.belong_city_id', $belongCityIdList]);
        } else {
            if (!$this->is_parent) {
                return $dataProvider;
            }
        }
        $query->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'direction', $this->direction]);

        return $dataProvider;
    }
}
