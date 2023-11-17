<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "zjs_api_count".
 *
 * @property int $id
 * @property int|null $count
 * @property string $logistic_no
 * @property string $create_time
 */
class ZjsApiCount extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'zjs_api_count';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['count'], 'integer'],
            [['logistic_no'], 'string'],
            [['create_time'], 'required'],
            [['create_time'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'count' => 'Count',
            'create_time' => 'Create Time',
        ];
    }
}
