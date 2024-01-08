<?php

namespace common\models;

use common\components\Utility;
use Yii;

/**
 * This is the model class for table "delivery_order_task".
 *
 * @property int $id 自增ID
 * @property string|null $file_path 文件路径
 * @property int|null $type 类型 1 订单 2 对账单 ...
 * @property int|null $order_type 单据类型
 * @property int|null $settlement_dimension 结算维度
 * @property int|null $status 执行状态 0 未执行 1 执行中 2 执行完成
 * @property string|null $result 结果
 * @property string|null $error_data 错误数据
 * @property string|null $apply_username 提交人用户名
 * @property string|null $apply_time 提交时间
 * @property string|null $start_time 执行开始时间
 * @property string|null $end_time 执行结束时间
 */
class DeliveryOrderTask extends \yii\db\ActiveRecord
{
    const STATUS_WAIT_UPDATE = 0;
    const STATUS_UPDATING = 1;
    const STATUS_UPDATED = 2;
    static $statusList = [
        self::STATUS_WAIT_UPDATE => '未执行',
        self::STATUS_UPDATING => '执行中',
        self::STATUS_UPDATED => '执行完成',
    ];

    const TYPE_ORDER = 1;
    const TYPE_CHECK_BILL = 2;
    static $typeList = [
        self::TYPE_ORDER => '订单',
        self::TYPE_CHECK_BILL => '对账单',
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'delivery_order_task';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'type', 'order_type', 'settlement_dimension'], 'integer'],
            [['file_path', 'result', 'error_data'], 'string'],
            [['apply_time', 'start_time', 'end_time'], 'safe'],
            [['apply_username'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'file_path' => '文件路径',
            'status' => '状态',
            'type' => '类型',
            'result' => '执行结果',
            'apply_username' => '申请人用户名',
            'apply_time' => '申请时间',
            'start_time' => '执行开始时间',
            'end_time' => '执行结束时间',
        ];
    }

    public static function getStatusName($status)
    {
        return isset(self::$statusList[$status]) ? self::$statusList[$status] : '无';
    }

    public static function getTypeName($type)
    {
        return isset(self::$typeList[$type]) ? self::$typeList[$type] : '无';
    }

    public static function reRun($id)
    {
        $model = self::findOne($id);
        $model->status = self::STATUS_WAIT_UPDATE;
        $model->result = '';
        $model->start_time = '';
        $model->end_time = '';
        $model->save();
        return true;
    }

    public static function getResult($result)
    {
        $return = [
            'success' => '  ',
            'msg' => '',
            'res' => ''
        ];
        if (!empty($result)) {
            $res = json_decode($result, true);
            if (isset($res['success']) && $res['success']) {
                $return['success'] = '成功';
            }
            if (!empty($res['return'])) {
                $return['res'] = '成功条数：' . $res['return']['successCount'] . ',失败条数：' . $res['return']['errorCount'];
            }
            if (!empty($res['return']['errorList'])) {
                if (is_array($res['return']['errorList'])) {
                    $return['res'] .= ',失败信息：' . implode('|', $res['return']['errorList']);
                } else {
                    $return['res'] .= ',失败信息：' . $res['return']['errorList'];
                }
            }
        }
        return Utility::truncateString(implode("。", $return), 20);
    }

    public static function getErrorDataHtml($errorData)
    {
        $errorDataHtml = '';
        if (!empty($errorData)) {
            $errorDataHtml = "<table><tr><td>快递单号</td><td>日期</td><td>库房号</td><td>订单号</td><td>包裹数量</td><td>包裹号</td><td>订单重量</td><td>订单重量（复重）</td><td>包裹重量</td><td>包裹重量（复重）</td><td>客户姓名</td><td>客户地址</td><td>客户电话</td><td>物流重量</td><td>物流公司</td><td>失败原因</td></tr>";
            $errorDataArr = json_decode($errorData, true, JSON_UNESCAPED_UNICODE);
            if (!empty($errorDataArr)) {
                foreach ($errorDataArr as $item) {
                    $errorDataHtml .= "<tr>";
                    $errorDataHtml .= "<td>" . $item[0] . "</td><td>" . $item[1] . "</td><td>" . $item[2] . "</td><td>" . $item[3] . "</td><td>" . $item[4] . "</td><td>" . $item[5] . "</td><td>" . $item[6] . "</td><td>" . $item[7] . "</td><td>" . $item[8] . "</td><td>" . $item[9] . "</td><td>" . $item[10] . "</td><td>" . $item[11] . "</td><td>" . $item[12] . "</td><td>" . $item[13] . "</td><td>" . $item[14] . "</td><td>" . $item[15] . "</td>";
                    $errorDataHtml .= "</tr>";
                }
            }
            $errorDataHtml .= "</table>";
        }
        return $errorDataHtml;
    }

}
