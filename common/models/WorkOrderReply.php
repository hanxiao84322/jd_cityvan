<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "work_order_reply".
 *
 * @property int $id
 * @property string $work_order_no 工单号
 * @property string $reply_content 回复内容
 * @property int $status 处理后状态
 * @property string $reply_name 回复人用户名
 * @property string $reply_time 回复时间
 */
class WorkOrderReply extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'work_order_reply';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['work_order_no', 'reply_content', 'reply_name', 'reply_time', 'status'], 'required'],
            [['reply_content'], 'string'],
            [['reply_time'], 'safe'],
            [['status'], 'integer'],
            [['work_order_no'], 'string', 'max' => 20],
            [['reply_name'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'work_order_no' => '工单号',
            'reply_content' => '回复内容',
            'status' => '处理后状态',
            'reply_name' => '回复人用户名',
            'reply_time' => '回复时间',
        ];
    }
}
