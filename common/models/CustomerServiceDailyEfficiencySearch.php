<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\CustomerServiceDailyEfficiency;

/**
 * CustomerServiceDailyEfficiencySearch represents the model behind the search form of `common\models\CustomerServiceDailyEfficiency`.
 */
class CustomerServiceDailyEfficiencySearch extends CustomerServiceDailyEfficiency
{
    public int $page_size = 20;
    public $create_time_start;
    public $create_time_end;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'create_time_start', 'create_time_end'], 'safe']
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
        $query = CustomerServiceDailyEfficiency::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        if (empty($this->create_time_start)) {
            $this->create_time_start = date('Y-m-d', strtotime('-1 day'));
        }
        $query->andWhere(['>=', 'date', $this->create_time_start]);

        if (empty($this->create_time_end)) {
            $this->create_time_end = date('Y-m-d', strtotime('-1 day'));
        }
        $query->andWhere(['<=', 'date', $this->create_time_end]);

        $query->andFilterWhere(['type' => $this->type]);
        $query->orderBy('work_order_create_num DESC,work_order_deal_num DESC,work_order_finished_num DESC,work_order_not_finished_num DESC');
//                        echo $query->createCommand()->getRawSql();exit;

        return $dataProvider;
    }
}
