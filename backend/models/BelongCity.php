<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "belong_city".
 *
 * @property int $id 自增ID
 * @property string $name 名称
 * @property int $status 状态 0 禁用 1 启用
 */
class BelongCity extends \yii\db\ActiveRecord
{
    const STATUS_CANCEL = 0;
    const STATUS_NORMAL = 1;
    public static $statusList = [
        self::STATUS_CANCEL => '禁用',
        self::STATUS_NORMAL => '正常',
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'belong_city';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required', 'message' => '名称不可以为空'],
            ['name', 'unique', 'targetClass' => '\backend\models\BelongCity', 'message' => '名称已存在.'],
            [['status'], 'integer'],
            [['name'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '名称',
            'status' => '状态',
        ];
    }
    /**
     * 获取状态文字.
     *
     * @param $status
     * @return mixed|string
     */
    public static function getShowStatusName($status)
    {
        return isset(self::$statusList[$status]) ? self::$statusList[$status] : '无';
    }

    public static function getAll()
    {
        $res = self::find()->where(['status' => self::STATUS_NORMAL])->asArray()->all();
        return $res;
    }

    public static function getListByJsonId($jsonId)
    {
        $list = [];
        $idList = json_decode($jsonId, true);
        $res = self::find()->where(['id' => $idList])->asArray()->all();
        if (!empty($res)) {
            foreach ($res as $value) {
                echo $value['name'] . ",";
            }
        }
    }

    /**
     *
     */
    public static function getNameById($id)
    {
        $res = self::findOne($id);
        return $res->name;
    }

    public static function getIdByName($name)
    {
        $res = self::findOne(['name' => $name]);
        return empty($res->id) ? '' : $res->id;
    }
}
