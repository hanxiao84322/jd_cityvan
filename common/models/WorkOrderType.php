<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "work_order_type".
 *
 * @property int $id
 * @property string $name 类型名称
 * @property string $description 类型说明
 */
class WorkOrderType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'work_order_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'description'], 'required'],
            [['name'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '类型名称',
            'description' => '类型说明',
        ];
    }

    public static function getAll()
    {
        return self::find()->asArray()->all();
    }

    public static function getTypeName($type)
    {
        return self::find()->select('name')->where(['id' => $type])->scalar();
    }
}
