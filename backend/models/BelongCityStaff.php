<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "belong_city_staff".
 *
 * @property int $id 自增ID
 * @property int $belong_city_id 厅点ID
 * @property string $code 人员编码
 * @property string $name 人员姓名
 */
class BelongCityStaff extends \yii\db\ActiveRecord
{
    public $belong_city;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'belong_city_staff';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['belong_city_id', 'name'], 'required'],
            [
                ['name', 'belong_city_id'],
                'unique',
                'targetAttribute' => ['name', 'belong_city_id'],
                'message' => '同一厅点下员工名称已存在.'
            ],
            ['code', 'unique', 'targetClass' => '\backend\models\BelongCityStaff', 'message' => '员工编码已存在.'],

            [['belong_city_id'], 'integer'],
            [['code', 'name'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'belong_city_id' => '厅点',
            'code' => '员工编码',
            'name' => '员工名称',
        ];
    }
}
