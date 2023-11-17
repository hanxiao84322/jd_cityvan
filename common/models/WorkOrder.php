<?php

namespace common\models;

use common\components\Utility;
use Yii;
/**
 * This is the model class for table "work_order".
 *
 * @property int $id 自增 ID
 * @property string $work_order_no 工单号
 * @property string $logistic_no 快递单号
 * @property string $order_no 订单号
 * @property string $shipping_no 包裹号
 * @property string $warehouse_code 仓库编码
 * @property int $logistic_id 物流公司ID
 * @property int $type 工单类型
 * @property int $priority  优先级 1 低 2 中 3 高
 * @property string|null $receive_name 客户姓名
 * @property string|null $receive_phone 客户电话
 * @property string|null $receive_address 客户地址
 * @property string|null $operate_username 负责人用户名-第一次处理工单的快递公司客服
 * @property string|null $assign_username 指派人用户名-创建工单的系统客服
 * @property string|null $description 工单描述
 * @property string|null $content 处理说明
 * @property string|null $file_path 附件
 * @property int|null $order_create_num 订单创建工单数量
 * @property int|null $customer_attention_level 客户关注等级
 * @property string|null $jd_work_order_no  京东工单号
 * @property string $target 创建标签 1系统工单,2普通工单,3京东工单
 * @property int $system_create 是否系统创建 0 否 1 是
 * @property int $ordinary_create 是否普通创建 0 否 1 是
 * @property int $jd_create 是否京东创建 0 否 1 是
 * @property float|null $penalty_amount 罚款金额
 * @property string $latest_reply 最后一条回复内容
 * @property int $status 工单状态 1 新增 2 指派处理中 3 答复完成 4 挂起 5 已关闭
 * @property int $customer_service_name 系统客服姓名
 * @property int $send_time 发货时间
 * @property string $create_time 创建时间
 * @property string|null $create_username 创建人用户名
 * @property string|null $update_time 更新时间
 * @property string|null $update_username 更新人用户名
 * @property string|null $finished_time 完成时间
 */

class WorkOrder extends \yii\db\ActiveRecord
{

    public string $logistic_company;
    public  $work_order_type_name;
    public $assign_name;

    public string $logistic_company_name;

    public array $files = [];

    const TYPE_HURRY_DELIVERY = 1;
    const TYPE_RETURN = 2;

    public static array $typeList = [
        self::TYPE_HURRY_DELIVERY => '催快递',
        self::TYPE_RETURN => '退货',
    ];

    const STATUS_WAIT_ALLOCATION = 1;
    const STATUS_WAIT_DEAL = 2;
    const STATUS_DEALT = 3;
    const STATUS_FINISHED = 4;
    const STATUS_PENDING = 5;

    public static array $statusList = [
        self::STATUS_WAIT_ALLOCATION => '新增',
        self::STATUS_WAIT_DEAL => '指派处理中',
        self::STATUS_DEALT => '答复完成',
        self::STATUS_FINISHED => '已完成',
        self::STATUS_PENDING => '挂起',
    ];

    const PRIORITY_LOW = 1;
    const PRIORITY_MIDDLE = 2;
    const PRIORITY_HIGH = 3;

    public static array $priorityList = [
        self::PRIORITY_LOW => '低',
        self::PRIORITY_MIDDLE => '中',
        self::PRIORITY_HIGH => '高',
    ];

    const CREATE_YES = 1;
    const CREATE_NO = 0;

    public static array $createList = [
        self::CREATE_YES => '是',
        self::CREATE_NO => '否',
    ];

    public int $total_num;
public $username;
public $name;
public $not_finished_num;
public $system_create_num;
public $ordinary_create_num;
public $jd_create_num;




    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'work_order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['work_order_no', 'logistic_no', 'order_no',  'logistic_id', 'create_time', 'type'], 'required'],
            [['logistic_id', 'type', 'priority', 'order_create_num', 'customer_attention_level', 'system_create', 'ordinary_create', 'jd_create', 'status'], 'integer'],
            [['penalty_amount'], 'number'],
            [['create_time', 'update_time', 'finished_time','send_time'], 'safe'],
            [['work_order_no', 'order_no', 'warehouse_code', 'receive_name', 'operate_username', 'assign_username', 'create_username', 'update_username','customer_service_name', 'shipping_no'], 'string', 'max' => 50],
            [['logistic_no', 'jd_work_order_no'], 'string', 'max' => 20],
            [['receive_phone', 'receive_address'], 'string', 'max' => 255],
            [['description', 'content', 'file_path', 'latest_reply'], 'string', 'max' => 500],
            [['target'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'work_order_no' => '工单号',
            'logistic_no' => '快递单号',
            'logistic_id' => '快递公司',
            'order_no' => '订单号',
            'shipping_no' => '包裹号',
            'warehouse_code' => '仓库编码',
            'type' => '类型',
            'priority' => '优先级',
            'receive_name' => '客户姓名',
            'receive_phone' => '客户手机号',
            'receive_address' => '客户地址',
            'assign_username' => '指派人',
            'assign_name' => '指派姓名',
            'operate_username' => '负责人',
            'description' => '描述',
            'content' => '处理说明',
            'file_path' => '相关文件',
            'order_create_num' => '订单创建工单次数',
            'customer_attention_level' => '客户关注等级',
            'jd_work_order_no' => '京东工单号',
            'target' => '标签',
            'penalty_amount' => '罚款金额',
            'system_create' => '是否系统创建',
            'ordinary_create' => '是否普通创建',
            'jd_create' => '是否京东创建',
            'latest_reply' => '最后一条回复内容',
            'status' => '状态',
            'customer_service_name' => '系统客服姓名',
            'send_time' => '发货时间',
            'create_time' => '创建时间',
            'create_username' => '创建人用户名',
            'update_time' => '更新时间',
            'update_username' => '更新人用户名',
            'finished_time' => '完成时间',
            'logistic_company_name' => '快递公司',
            'work_order_type_name' => '工单类型'
        ];
    }

    public static function generateId()
    {
        $lasted = self::find()->limit(1)->orderBy('create_time desc')->asArray()->one();
        $p = "WO" . date('ymd');
        if ($lasted && strstr($lasted['work_order_no'], $p)) {
            $last_id = $lasted['work_order_no'];
            $count = intval(substr($last_id, strlen($last_id) - 4));
        } else {
            $count = 0;
        }
        return $p . str_pad(++$count, 4, '0', STR_PAD_LEFT);
    }

    public static function getTypeName($type)
    {
        return isset(self::$typeList[$type]) ? self::$typeList[$type] : '无';
    }

    public static function getStatusName($status)
    {
        return isset(self::$statusList[$status]) ? self::$statusList[$status] : '无';
    }

    public static function getPriorityName($priority)
    {
        return isset(self::$priorityList[$priority]) ? self::$priorityList[$priority] : '无';
    }

    public static function getCreateName($create)
    {
        return isset(self::$createList[$create]) ? self::$createList[$create] : '无';
    }

    public static function getCreateNumByLogisticNo($logisticNo)
    {
        return self::find()->where(['logistic_no'=>$logisticNo])->count();
    }

    public static function getCountByUsername($username)
    {
        return self::find()->where(['receive_name'=>$username])->count();

    }

    public static function  batchUpdate($excelData, $username)
    {
        $return = [
            'successCount' => 0,
            'errorCount' => 0,
            'errorList' => '',
        ];
        $errorList = [];
        foreach ($excelData as $line => $item) {
            try {
                $createTime = empty($item[0]) ? date('Y-m-d H:i:s', time()) : $item[0];
                $customerServiceName = !empty($item[1]) ? (string)$item[1] : '系统客服';
                $sendTime = empty($item[2]) ? date('Y-m-d H:i:s', time()) : $item[2];
                $orderNo = !empty($item[4]) ? (string)$item[4] : 'EMPTY';
                $shippingNo = !empty($item[5]) ? (string)$item[5] : 'EMPTY';
                $receiverName = (string)$item[8];
                $receiverAddress = (string)$item[9];
                $logisticNo = !empty($item[10]) ? (string)$item[10] : 'EMPTY';
                $logisticCompany = !empty($item[10]) ? (string)$item[10] : '无';
                $receiverPhone = (string)$item[12];
                $typeName = !empty($item[13]) ? (string)$item[13] : '催单';
                $content = !empty($item[14]) ? (string)$item[14] : '回复内容';
                $status = !empty($item[15]) ? (string)$item[15] : '已完成';

                $workOrderTypeRes = WorkOrderType::find()->where(['name' => $typeName])->asArray()->one();

                //快递公司不存在加一个
                if (empty($workOrderTypeRes)) {
                    $workOrderTypeModel = new WorkOrderType();
                    $workOrderTypeModel->name = $typeName;
                    $workOrderTypeModel->description = $typeName;
                    if (!$workOrderTypeModel->save()) {
                        throw new \Exception(Utility::arrayToString($workOrderTypeModel->getErrors()));
                    }
                    $type = $workOrderTypeModel->attributes['id'];
                } else {
                    $type = $workOrderTypeRes['id'];
                }

                if (empty($logisticNo)) {
                    if (!empty($orderNo)) {
                        $logisticNo = DeliveryOrder::getLogisticNoByOrderNo($orderNo);
                    } elseif (!empty($shippingNo)) {
                        $logisticNo = DeliveryOrder::getLogisticNoByShippingNo($shippingNo);
                    } else {
                        $logisticNo = '';
                    }
                }
                if (empty($logisticNo)) {
                    $logisticNo = 'EMPTY';
                }

                if (empty($logisticCompany)) {
                    $logisticId = DeliveryOrder::getLogisticId($logisticNo);
                } else {
                    $logisticCompanyRes = LogisticCompany::find()->where(['company_name' => $logisticCompany])->asArray()->one();

                    //快递公司不存在加一个
                    if (empty($logisticCompanyRes)) {
                        $logisticCompanyModel = new LogisticCompany();
                        $logisticCompanyModel->company_name = $logisticCompany;
                        if (!$logisticCompanyModel->save()) {
                            throw new \Exception(Utility::arrayToString($logisticCompanyModel->getErrors()));
                        }
                        $logisticId = $logisticCompanyModel->attributes['id'];
                    } else {
                        $logisticId = $logisticCompanyRes['id'];
                    }
                }

                $workOrderExists = WorkOrder::find()->where(['logistic_no' => $logisticNo])->exists();
                if (!$workOrderExists) {
                    $workOrderModel = new WorkOrder();
                    $workOrderModel->work_order_no = WorkOrder::generateId();
                    $workOrderModel->create_time = $createTime;
                    $workOrderModel->logistic_no = $logisticNo;
                    $workOrderModel->warehouse_code = 'cd';
                    $workOrderModel->order_no = $orderNo;
                    $workOrderModel->logistic_id = $logisticId;
                    $workOrderModel->type = $type;
                    $workOrderModel->priority = WorkOrder::PRIORITY_MIDDLE;
                    $workOrderModel->operate_username = $logisticCompany;
                    $workOrderModel->description = $typeName;
                    $workOrderModel->order_create_num = WorkOrder::getCreateNumByLogisticNo($logisticNo);
                    $workOrderModel->type = $type;
                    $workOrderModel->receive_name = $receiverName;
                    $workOrderModel->receive_address = $receiverAddress;
                    $workOrderModel->receive_phone = $receiverPhone;
                    $workOrderModel->customer_attention_level = ImportantCustomer::getLevelByNameAndPhone($receiverName, $receiverPhone);
                    $workOrderModel->status = $status == '需跟进' ? WorkOrder::STATUS_PENDING : WorkOrder::STATUS_FINISHED;
                    $workOrderModel->jd_work_order_no = '';
                    $workOrderModel->ordinary_create = 1;
                    $workOrderModel->create_username = 'system';
                    $workOrderModel->customer_service_name = $customerServiceName;
                    $workOrderModel->send_time = $sendTime;
                    if (!$workOrderModel->save()) {
                        throw new \Exception(Utility::arrayToString($workOrderModel->getErrors()));
                    }
                    $workOrderNo = $workOrderModel->work_order_no;
                } else {
                    $workOrderNo = WorkOrder::getWorkOrderNoByLogisticNo($logisticNo);
                }
                $workOrderReplyModel = new WorkOrderReply();
                $workOrderReplyModel->work_order_no = $workOrderNo;
                $workOrderReplyModel->reply_content = $content;
                $workOrderReplyModel->reply_time = $createTime;
                $workOrderReplyModel->status = $status == '需跟进' ? WorkOrder::STATUS_PENDING : WorkOrder::STATUS_FINISHED;
                $workOrderReplyModel->reply_name = $customerServiceName;
                if (!$workOrderReplyModel->save()) {
                    throw new \Exception(Utility::arrayToString($workOrderReplyModel->getErrors()));
                }
                if ($status != '需跟进') {
                    WorkOrder::updateAll(['finished_time' => $createTime, 'update_time' => $createTime, 'status' => WorkOrder::STATUS_FINISHED, 'update_username' => 'system'],['work_order_no' => $workOrderNo]);
                } else {
                    WorkOrder::updateAll([ 'update_time' => $createTime, 'status' => WorkOrder::STATUS_FINISHED, 'update_username' => 'system'],['work_order_no' => $workOrderNo]);
                }
                if (!empty($receiverName) && !empty($receiverPhone)) {
                    $importantCustomerExists = ImportantCustomer::find()->where(['name' => $receiverName, 'phone' => $receiverPhone])->exists();
                    if (!$importantCustomerExists) {
                        $importantCustomerModel = new ImportantCustomer();
                        $importantCustomerModel->name = $receiverName;
                        $importantCustomerModel->phone = $receiverPhone;
                        $importantCustomerModel->address = $receiverAddress;
                        $importantCustomerModel->work_order_num = 1;
                        $importantCustomerModel->complaint_type = $typeName;
                        $importantCustomerModel->create_name = 'system';
                        $importantCustomerModel->create_time = date('Y-m-d H:i:s', time());
                    } else {
                        $importantCustomerModel = ImportantCustomer::findOne(['phone' => $receiverPhone, 'name' => $receiverName]);
                        $importantCustomerModel->work_order_num = WorkOrder::getCountByUsername($receiverName);
                        if (!in_array($typeName, explode(',', $importantCustomerModel->complaint_type))) {
                            $importantCustomerModel->complaint_type = $importantCustomerModel->complaint_type . ',' . $typeName;
                        }
                        $importantCustomerModel->level = ImportantCustomer::getLevelByCount($importantCustomerModel->work_order_num);
                        $importantCustomerModel->update_name = 'system';
                        $importantCustomerModel->update_time = date('Y-m-d H:i:s', time());
                    }
                    if (!$importantCustomerModel->save()) {
                        throw new \Exception(Utility::arrayToString($importantCustomerModel->getErrors()));
                    }
                }


                $return['successCount']++;

            } catch (\Exception $e) {
                $return['errorCount']++;
                $errorList[] = '第' . $line . '行失败，' . $e->getMessage();
                $return['errorList'] = $errorList;
            }
        }
        return $return;
    }

    public static function getWorkOrderNoByLogisticNo($logisticNo)
    {
        return self::find()->select('work_order_no')->where(['logistic_no' => $logisticNo])->scalar();
    }

    public static function getStatus($logisticNo)
    {
        return self::find()->select('status')->where(['logistic_no' => $logisticNo])->scalar();
    }

    public static function getRetentionTotalNum()
    {
        return self::find()->select('id')->where(['<>', 'status', self::STATUS_FINISHED])->count();
    }
}
