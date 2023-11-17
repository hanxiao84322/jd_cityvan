<?php

namespace backend\models;

use backend\models\OrderFiles;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * OrderFilesSearch represents the model behind the search form of `backend\models\OrderFiles`.
 */
class OrderFilesSearch extends OrderFiles
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'type', 'order_id'], 'integer'],
            [['file_path', 'upload_no'], 'safe'],
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
        $query = OrderFiles::find();

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
        if ($this->type == OrderFiles::TYPE_WORK_ORDER) {
            $query->andFilterWhere(['in', 'type', [OrderFiles::TYPE_WORK_ORDER, OrderFiles::TYPE_WORK_ORDER_REPLY]]);
        } else {
            $query->andFilterWhere(['type' => $this->type]);
        }


        // grid filtering conditions
        $query->andFilterWhere([
            'order_id' => $this->order_id,
        ]);

        $query->andFilterWhere(['like', 'file_path', $this->file_path])
            ->andFilterWhere(['like', 'upload_no', $this->upload_no]);
//                echo $query->createCommand()->getRawSql();exit;

        return $dataProvider;
    }
}
