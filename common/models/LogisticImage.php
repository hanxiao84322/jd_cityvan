<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "logistic_image".
 *
 * @property int $id 自增ID
 * @property string|null $device_id 设备ID
 * @property int|null $logistic_no 快递单号
 * @property string|null $image_base64_str 图片base64信息
 * @property string|null $create_time 推送时间
 */
class LogisticImage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'logistic_image';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['image_base64_str','logistic_no'], 'string'],
            [['create_time'], 'safe'],
            [['device_id'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'device_id' => 'Device ID',
            'logistic_no' => 'Logistic No',
            'image_base64_str' => 'Image Base64 Str',
            'create_time' => 'Create Time',
        ];
    }
}
