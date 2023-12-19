<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "logistic_company".
 *
 * @property int $id 自增ID
 * @property string $company_name 供应商名称
 * @property string $status 状态
 * @property string $responsible_area 负责区域
 */
class LogisticCompany extends \yii\db\ActiveRecord
{
    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 2;

    public static array $statusList = [
        self::STATUS_ENABLE => '启用',
        self::STATUS_DISABLE => '禁用',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'logistic_company';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['company_name'], 'required'],
            ['company_name', 'unique', 'targetClass' => '\common\models\LogisticCompany', 'message' => '名称已存在.'],

            [['company_name'], 'string', 'max' => 50],
            [['status'], 'integer'],
            [['responsible_area'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '快递公司ID',
            'company_name' => '快递公司名称',
            'status' => '状态',
            'responsible_area' => 'Responsible Area',
        ];
    }

    public static function getNameById($id)
    {
        $model = self::findOne($id);
        return isset($model->company_name) ? $model->company_name : '';
    }

    public static function getAll()
    {
        return self::find()->where(['status' => self::STATUS_ENABLE])->asArray()->all();
    }

    public static function getStatusName($status)
    {
        return isset(self::$statusList[$status]) ? self::$statusList[$status] : '无';
    }

    public static function getListByJsonId($jsonId)
    {
        $idList = json_decode($jsonId, true);
        $res = self::find()->where(['id' => $idList])->asArray()->all();
        if (!empty($res)) {
            foreach ($res as $value) {
                echo $value['company_name'] . ",";
            }
        }
    }
}
