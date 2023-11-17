<?php

namespace common\models;

use backend\models\UserBackend;
use common\components\Utility;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * WorkOrderSearch represents the model behind the search form of `common\models\WorkOrder`.
 */
class WorkOrderSearch extends WorkOrder
{
    public int $page_size = 20;
    public int $is_not_finished = 0;
    public string $create_time_start = '';
    public string $create_time_end = '';
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'type', 'priority', 'status', 'is_not_finished'], 'integer'],
            [['work_order_no', 'order_no', 'receive_name', 'receive_phone', 'receive_address', 'operate_username','assign_username', 'description', 'content', 'file_path', 'create_time', 'create_username', 'update_time', 'update_username', 'finished_time', 'warehouse_code', 'logistic_id', 'system_create', 'jd_create', 'ordinary_create', 'logistic_no', 'create_time_start', 'create_time_end', 'jd_work_order_no'], 'safe'],
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
     * @param array $dataPower
     *
     * @return ActiveDataProvider
     */
    public function search($params, $dataPower)
    {
        $query = WorkOrder::find()->select("wo.*,wot.name as work_order_type_name, lc.company_name as logistic_company_name, ub.name as assign_name")->alias('wo')->leftJoin(WorkOrderType::tableName() . ' wot', 'wo.type = wot.id')->leftJoin(LogisticCompany::tableName() . ' lc', 'wo.logistic_id = lc.id')->leftJoin(UserBackend::tableName() . ' ub', 'ub.username = wo.assign_username');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }

        if (!empty($dataPower)) {
            if (isset($dataPower['logisticIds'])) {
                $query->andFilterWhere(['in', 'wo.logistic_id', json_decode($dataPower['logisticIds'], true)]);
                $query->andFilterWhere(['!=', 'wo.status', self::STATUS_DEALT]);
            }
        }
        if (empty($this->status)) {
            $query->andFilterWhere(['!=', 'wo.status', self::STATUS_FINISHED]);
        }

        if (empty($this->create_time_start)) {
            $this->create_time_start = date('Y-m-d 00:00:00', strtotime('-30 day'));
        }
        if (empty($this->create_time_end)) {
            $this->create_time_end = date('Y-m-d 23:59:59', time());
        }
        if (!empty($this->create_time_start)) {
            $query->andWhere(['>=', 'wo.create_time', $this->create_time_start]);
        }
        if (!empty($this->create_time_end)) {
            $query->andWhere(['<=', 'wo.create_time', $this->create_time_end]);
        }
        $query->andFilterWhere(['wo.logistic_no' => $this->logistic_no]);

        $query->andFilterWhere(['wo.order_no' => $this->order_no]);
        $query->andFilterWhere(['wo.work_order_no' => $this->work_order_no]);
        $query->andFilterWhere(['wo.warehouse_code' => $this->warehouse_code]);
        $query->andFilterWhere(['wo.logistic_id' => $this->logistic_id]);
        $query->andFilterWhere(['wo.assign_username' => $this->assign_username]);
        $query->andFilterWhere(['wo.create_username' => $this->create_username]);
        $query->andFilterWhere(['wo.operate_username' => $this->operate_username]);
        $query->andFilterWhere(['wo.system_create' => $this->system_create]);
        $query->andFilterWhere(['wo.ordinary_create' => $this->ordinary_create]);
        $query->andFilterWhere(['wo.jd_create' => $this->jd_create]);
        $query->andFilterWhere(['wo.jd_work_order_no' => $this->jd_work_order_no]);
        $query->andFilterWhere(['wo.type' => $this->type]);
        $query->andFilterWhere(['wo.status' => $this->status]);

        $query->orderBy('create_time DESC');

//        echo $query->createCommand()->getRawSql();exit;

        return $dataProvider;
    }


    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param array $dataPower
     *
     * @return ActiveDataProvider
     */
    public function exportData($params, $dataPower)
    {
        $query = WorkOrder::find()->select("wo.*,wot.name as work_order_type_name, lc.company_name as logistic_company_name")->alias('wo')->leftJoin(WorkOrderType::tableName() . ' wot', 'wo.type = wot.id')->leftJoin(LogisticCompany::tableName() . ' lc', 'wo.logistic_id = lc.id');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }

        if (!empty($dataPower)) {
            if (isset($dataPower['logisticIds'])) {
                $query->andFilterWhere(['in', 'wo.logistic_id', json_decode($dataPower['logisticIds'], true)]);
            }
        }
        if ($this->is_not_finished) {
            $query->andFilterWhere(['!=', 'wo.status', self::STATUS_FINISHED]);
        }
        if (empty($this->create_time_start)) {
            $this->create_time_start = date('Y-m-d 00:00:00', strtotime('-30 day'));
        }
        if (empty($this->create_time_end)) {
            $this->create_time_end = date('Y-m-d 23:59:59', time());
        }
        if (!empty($this->create_time_start)) {
            $query->andWhere(['>=', 'wo.create_time', $this->create_time_start]);
        }
        if (!empty($this->create_time_end)) {
            $query->andWhere(['<=', 'wo.create_time', $this->create_time_end]);
        }
        $query->andFilterWhere(['wo.logistic_no' => $this->logistic_no]);

        $query->andFilterWhere(['wo.order_no' => $this->order_no]);
        $query->andFilterWhere(['wo.work_order_no' => $this->work_order_no]);
        $query->andFilterWhere(['wo.warehouse_code' => $this->warehouse_code]);
        $query->andFilterWhere(['wo.logistic_id' => $this->logistic_id]);
        $query->andFilterWhere(['wo.assign_username' => $this->assign_username]);
        $query->andFilterWhere(['wo.create_username' => $this->create_username]);
        $query->andFilterWhere(['wo.operate_username' => $this->operate_username]);
        $query->andFilterWhere(['wo.system_create' => $this->system_create]);
        $query->andFilterWhere(['wo.ordinary_create' => $this->ordinary_create]);
        $query->andFilterWhere(['wo.jd_create' => $this->jd_create]);
        $query->andFilterWhere(['wo.type' => $this->type]);
        $query->andFilterWhere(['wo.status' => $this->status]);

        $query->orderBy('create_time DESC');

//        echo $query->createCommand()->getRawSql();exit;

        $result = $query->asArray()->all();
        $data = [];
        $exportData = [];
        if (!empty($result)) {
            foreach ($result as $value) {
                $data[] = $value['logistic_no'];
                $data[] = $value['work_order_no'];
                $data[] = $value['order_no'];
                $data[] = $value['jd_work_order_no'];
                $data[] = $value['assign_username'];
                $data[] = $value['operate_username'];
                $data[] = $value['warehouse_code'];
                $data[] = $value['logistic_company_name'];
                $data[] = $value['work_order_type_name'];
                $data[] = WorkOrder::getPriorityName($value['priority']);
                $data[] = $value['receive_name'];
                $data[] = $value['receive_phone'];
                $data[] = $value['receive_address'];
                $data[] = $value['order_create_num'];
                $data[] = WorkOrder::getCreateName($value['system_create']);
                $data[] = WorkOrder::getCreateName($value['ordinary_create']);
                $data[] = WorkOrder::getCreateName($value['jd_create']);
                $data[] = $value['penalty_amount'];
                $data[] = $value['latest_reply'];
                $data[] = WorkOrder::getStatusName($value['status']);
                $data[] = $value['create_time'];
                $data[] = $value['create_username'];
                $data[] = $value['finished_time'];
                $exportData[] = $data;
                unset($data);
            }
        }
        $fileName = '工单信息导出-' . date('YmdHi');
        $header = self::getExportDataHeader();
        Utility::exportData($exportData, $header, $fileName, $fileName);
        exit();
    }


    public function searchRetention($params)
    {
        $this->load($params);
        if (empty($this->type)) {
            $this->type = UserBackend::TYPE_CUSTOMER_SERVICE;
        }
        if ($this->type == UserBackend::TYPE_CUSTOMER_SERVICE) {
            $query = WorkOrder::find()->alias('wo')->select("ub.username, ub.type,ub.name,  count(*) as not_finished_num, sum(wo.system_create) AS system_create_num,sum(wo.ordinary_create) AS ordinary_create_num,sum(wo.jd_create) AS jd_create_num")->leftJoin(UserBackend::tableName() . ' ub', 'wo.assign_username = ub.username')->groupBy('ub.username');
        } else {
            $query = WorkOrder::find()->alias('wo')->select("ub.username, ub.type,ub.name,  count(*) as not_finished_num, sum(wo.system_create) AS system_create_num,sum(wo.ordinary_create) AS ordinary_create_num,sum(wo.jd_create) AS jd_create_num")->leftJoin(UserBackend::tableName() . ' ub', 'wo.operate_username = ub.username')->groupBy('ub.username');
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->andFilterWhere(['ub.type' => $this->type]);
        $query->andFilterWhere(['<>', 'wo.status', WorkOrder::STATUS_FINISHED]);

//                echo $query->createCommand()->getRawSql();exit;

        return $dataProvider;
    }

    public static function getExportDataHeader()
    {
        return ['邮件单号',
            '工单号',
            '订单号',
            '京东订单号',
            '指派人',
            '负责人',
            '仓库编码',
            '快递公司',
            '工单类型',
            '优先级',
            '客户姓名',
            '客户电话',
            '客户地址',
            '订单创建工单次数',
            '是否系统创建',
            '是否普通创建',
            '是否京东创建',
            '罚款金额',
            '最后一条回复内容',
            '状态',
            '创建时间',
            '创建人用户名',
            '完成时间',
            ];
    }
}
