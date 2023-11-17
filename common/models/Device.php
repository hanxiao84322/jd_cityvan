<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "device".
 *
 * @property int $id 自增ID
 * @property string|null $code 编码
 * @property string|null $belong_city_id 所在地(厅点)
 * @property string|null $direction 方向
 */
class Device extends \yii\db\ActiveRecord
{
    public $belong_city_name;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'device';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code'], 'required', 'message' => '编码不可以为空'],
            ['code', 'unique', 'targetClass' => '\common\models\Device', 'message' => '编码已存在.'],
            [['code'], 'string', 'max' => 20],
            [['belong_city_id'], 'integer'],
            [['direction'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => '编码',
            'belong_city_id' => '所在地(厅点)',
            'belong_city_name' => '所在地(厅点)',
            'direction' => '方向',
        ];
    }
}
