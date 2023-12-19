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
 * @property int|null $status 执行状态 0 未执行 1 执行中 2 执行完成
 * @property string|null $result 结果
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
    const TYPE_LOGISTIC_COMPANY_CHECK_BILL = 2;
    static $typeList = [
        self::TYPE_ORDER => '订单',
        self::TYPE_LOGISTIC_COMPANY_CHECK_BILL => '对账单',
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
            [['status', 'type', 'order_type'], 'integer'],
            [['file_path', 'result'], 'string'],
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

}
