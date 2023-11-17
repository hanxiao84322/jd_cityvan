<?php

namespace common\models;

use common\components\Utility;
use Yii;

/**
 * This is the model class for table "delivery_image".
 *
 * @property int $id 自增ID
 * @property string $logistic_no 快递单号
 * @property string|null $image_data 图片解析数据
 * @property string $create_time 创建时间
 */
class DeliveryImage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'delivery_image';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['image_data'], 'string'],
            [['create_time'], 'safe'],
            [['logistic_no'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'logistic_no' => '快递单号',
            'image_data' => 'Image Data',
            'create_time' => '创建时间',
        ];
    }

    public static function getImageData($imageData)
    {
        $imageArr = json_decode($imageData, true);
        $imageStr = Utility::arrayToString($imageArr);
        return $imageData;
    }
}