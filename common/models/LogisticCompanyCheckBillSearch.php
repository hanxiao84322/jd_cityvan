<?php

namespace common\models;

use common\components\Utility;
use common\models\LogisticCompanyCheckBill;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * LogisticCompanyCheckBillSearch represents the model behind the search form of `common\models\LogisticCompanyCheckBill`.
 */
class LogisticCompanyCheckBillSearch extends LogisticCompanyCheckBill
{
    public $page_size = 20;
    public $create_time_start;
    public $create_time_end;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'logistic_id', 'logistic_company_order_num', 'system_order_num', 'status'], 'integer'],
            [['logistic_company_check_bill_no', 'warehouse_code', 'date', 'create_username', 'create_time', 'update_username', 'update_time', 'create_time_start', 'create_time_end', 'note'], 'safe'],
            [['logistic_company_order_price', 'system_order_price'], 'number'],
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
        $query = LogisticCompanyCheckBill::find()->select("lccb.*,lc.company_name as logistic_company_name")->alias('lccb')->leftJoin(LogisticCompany::tableName() . ' lc', 'lccb.logistic_id = lc.id');

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
            'lccb.logistic_id' => $this->logistic_id,
            'lccb.warehouse_code' => $this->warehouse_code,
            'lccb.status' => $this->status,
            'lccb.logistic_company_check_bill_no' => $this->logistic_company_check_bill_no,
        ]);
        if (empty($this->create_time_start)) {
            $this->create_time_start = date('Y-m-d', strtotime('-1 day'));
        }
        if (empty($this->create_time_end)) {
            $this->create_time_end = date('Y-m-d', strtotime('+1 day'));
        }
        $query->andWhere(['>=', 'lccb.create_time', $this->create_time_start]);
        $query->andWhere(['<=', 'lccb.create_time', $this->create_time_end]);

        $query->orderBy('lccb.create_time DESC');

//                echo $query->createCommand()->getRawSql();exit;

        return $dataProvider;
    }

    public function exportData($params)
    {
        $query = LogisticCompanyCheckBill::find()->select("lccb.*,lc.company_name as logistic_company_name")->alias('lccb')->leftJoin(LogisticCompany::tableName() . ' lc', 'lccb.logistic_id = lc.id');

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
            'lccb.logistic_id' => $this->logistic_id,
            'lccb.warehouse_code' => $this->warehouse_code,
            'lccb.status' => $this->status,
            'lccb.logistic_company_check_bill_no' => $this->logistic_company_check_bill_no,
        ]);
        if (empty($this->create_time_start)) {
            $this->create_time_start = date('Y-m-d 00:00:00', time());
        }
        if (empty($this->create_time_end)) {
            $this->create_time_end = date('Y-m-d 23:59:59', time());
        }
        $query->andWhere(['>=', 'lccb.create_time', $this->create_time_start]);
        $query->andWhere(['<=', 'lccb.create_time', $this->create_time_end]);
        $query->orderBy('lccb.create_time DESC');

        $result = $query->asArray()->all();
        $data = [];
        $exportData = [];
        if (!empty($result)) {
            foreach ($result as $value) {
                $data[] = $value['logistic_company_check_bill_no'];
                $data[] = $value['logistic_company_name'];
                $data[] = $value['warehouse_code'];
                $data[] = $value['date'];
                $data[] = $value['logistic_company_order_num'];
                $data[] = $value['system_order_num'];
                $data[] = $value['system_order_price'];
                $data[] = self::getStatusName($value['status']);
                $data[] = $value['create_username'];
                $data[] = $value['create_time'];
                $exportData[] = $data;
                unset($data);
            }
        }
        $fileName = '对账单信息导出-' . date('YmdHi');
        $header = self::getExportDataHeader();
        Utility::exportData($exportData, $header, $fileName, $fileName);
        exit();
    }
    public static function getExportDataHeader()
    {
        return ['对账单号',
            '快递公司',
            '仓库编码',
            '生成日期',
            '导入数据',
            '有效数据',
            '有效金额',
            '状态',
            '创建人用户名',
            '创建时间'];
    }

}
