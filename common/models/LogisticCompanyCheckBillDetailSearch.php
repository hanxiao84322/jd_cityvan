<?php

namespace common\models;

use common\components\Utility;
use common\models\LogisticCompanyCheckBillDetail;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * LogisticCompanyCheckBillDetailSearch represents the model behind the search form of `common\models\LogisticCompanyCheckBillDetail`.
 */
class LogisticCompanyCheckBillDetailSearch extends LogisticCompanyCheckBillDetail
{
    public  $page_size = 20;
    public $is_diff_status;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'logistic_id', 'status'], 'integer'],
            [['logistic_company_check_bill_no', 'warehouse_code', 'logistic_no', 'note', 'create_username', 'create_time', 'is_diff_status'], 'safe'],
            [['weight', 'price', 'system_weight', 'system_price'], 'number'],
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
        $query = LogisticCompanyCheckBillDetail::find()->select("lccbd.*,lc.company_name as logistic_company_name")->alias('lccbd')->leftJoin(LogisticCompany::tableName() . ' lc', 'lccbd.logistic_id = lc.id');;

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        if ($this->is_diff_status) {
            $query->andFilterWhere(['not in', 'lccbd.status', [LogisticCompanyCheckBillDetail::STATUS_SAME,LogisticCompanyCheckBillDetail::STATUS_WEIGHT_DIFF, LogisticCompanyCheckBillDetail::STATUS_PRICE_DIFF]]);
        }
        $query->andFilterWhere([
            'lccbd.status' => $this->status,
            'lccbd.logistic_company_check_bill_no' => $this->logistic_company_check_bill_no
        ]);
//                echo $query->createCommand()->getRawSql();exit;

        return $dataProvider;
    }


    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function exportData($params)
    {
        $query = LogisticCompanyCheckBillDetail::find()->select("lccbd.*,lc.company_name as logistic_company_name")->alias('lccbd')->leftJoin(LogisticCompany::tableName() . ' lc', 'lccbd.logistic_id = lc.id');;

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        if ($this->is_diff_status) {
            $query->andFilterWhere(['not in', 'lccbd.status', [LogisticCompanyCheckBillDetail::STATUS_SAME,LogisticCompanyCheckBillDetail::STATUS_WEIGHT_DIFF, LogisticCompanyCheckBillDetail::STATUS_PRICE_DIFF]]);
        }
        $query->andFilterWhere([
            'lccbd.status' => $this->status,
            'lccbd.logistic_company_check_bill_no' => $this->logistic_company_check_bill_no
        ]);
//                echo $query->createCommand()->getRawSql();exit;
        $result = $query->asArray()->all();

        $data = [];
        $exportData = [];
        if (!empty($result)) {
            foreach ($result as $value) {
                $data[] = $value['logistic_company_check_bill_no'];
                $data[] = $value['logistic_company_name'];
                $data[] = $value['warehouse_code'];
                $data[] = $value['logistic_no'];
                $data[] = $value['weight'];
                $data[] = $value['price'];
                $data[] = $value['system_weight'];
                $data[] = $value['system_price'];
                $data[] = self::getStatusName($value['status']);
                $data[] = $value['note'];
                $data[] = $value['create_username'];
                $data[] = $value['create_time'];
                $exportData[] = $data;
                unset($data);
            }
        }
        $fileName = '对账单明细信息导出-' . date('YmdHi');
        $header = self::getExportDataHeader();
        Utility::exportData($exportData, $header, $fileName, $fileName);
        exit();
    }
    public static function getExportDataHeader()
    {
        return ['对账单号',
            '快递公司',
            '仓库编码',
            '快递单号',
            '导入重量',
            '导入金额',
            '系统重量',
            '系统金额',
            '状态',
            '备注',
            '创建人用户名',
            '创建时间'];
    }

}
