<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\LogisticCompanyFeeRules;

/**
 * LogisticCompanyFeeRulesSearch represents the model behind the search form of `common\models\LogisticCompanyFeeRules`.
 */
class LogisticCompanyFeeRulesSearch extends LogisticCompanyFeeRules
{
    public $page_size = 20;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'logistic_id', 'weight_round_rule', 'continue_weight_round_rule'], 'integer'],
            [['warehouse_code', 'province', 'city', 'district', 'continue_weight_rule', 'create_username', 'create_time', 'update_username', 'update_time', 'type'], 'safe'],
            [['weight', 'price'], 'number'],
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
        $query = LogisticCompanyFeeRules::find()->select("lcfr.*,lc.company_name as logistic_company_name")->alias('lcfr')->leftJoin(LogisticCompany::tableName() . ' lc', 'lcfr.logistic_id = lc.id');;

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
            'lcfr.logistic_id' => $this->logistic_id,
            'lcfr.type' => $this->type,
            'lcfr.warehouse_code' => $this->warehouse_code,
        ]);

        return $dataProvider;
    }
}
