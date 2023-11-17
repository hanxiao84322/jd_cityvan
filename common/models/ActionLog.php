<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "action_log".
 *
 * @property int $id
 * @property string|null $target 单号
 * @property int|null $level
 * @property string|null $category
 * @property float|null $log_time
 * @property string|null $prefix
 * @property string|null $message
 * @property string|null $data
 * @property string|null $operation_username 操作人用户名
 * @property string|null $operation_time 操作时间
 */
class ActionLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'action_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['level'], 'integer'],
            [['log_time'], 'number'],
            [['prefix', 'message'], 'string'],
            [['operation_time'], 'safe'],
            [['target'], 'string', 'max' => 100],
            [['category'], 'string', 'max' => 255],
            [['operation_username'], 'string', 'max' => 50],
            [['data'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'target' => 'Logistic No',
            'level' => 'Level',
            'category' => 'Category',
            'log_time' => 'Log Time',
            'prefix' => 'Prefix',
            'message' => 'Message',
            'operation_username' => 'Operation Username',
            'operation_time' => 'Operation Time',
        ];
    }

    public static function log($target, $category, $message, $data, $username = 'system')
    {
        $model = new self();
        $model->target = $target;
        $model->category = $category;
        $model->message = $message;
        $model->data = $data;
        $model->operation_username = $username;
        $model->operation_time = date('Y-m-d H:i:s', time());
        if (!$model->save()) {
            return $model->getErrors();
        }
        return true;
    }
}
