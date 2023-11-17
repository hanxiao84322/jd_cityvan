<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "order_files".
 *
 * @property int $id
 * @property int $type 单据类型 1 工单
 * @property int $order_id 单据 ID
 * @property string $file_path 上传文件地址
 * @property string $upload_no 上传文件编号
 * @property string $name 附件名称
 * @property string $create_username 创建人用户名
 * @property string $create_time 创建时间
 */
class OrderFiles extends \yii\db\ActiveRecord
{
    public $imageFile;

    const TYPE_WORK_ORDER = 1;
    const TYPE_WORK_ORDER_REPLY = 2;
    const TYPE_DELIVERY_ADJUST_ORDER = 3;

    public static array $typeList = [
        self::TYPE_WORK_ORDER => '工单附件',
        self::TYPE_WORK_ORDER_REPLY => '工单回复附件',
        self::TYPE_DELIVERY_ADJUST_ORDER => '调整单附件',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_files';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'file_path'], 'required'],
            [['type', 'order_id'], 'integer'],
            [['file_path','name', 'create_username', 'create_time'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'order_id' => 'Order ID',
            'file_path' => '附件信息',
            'upload_no' => 'Upload No',
            'name' => '附件名称',
            'create_username' => '上传人用户名',
            'create_time' => '上传时间',
        ];
    }

}
