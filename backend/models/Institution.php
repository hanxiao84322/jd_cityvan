<?php

namespace backend\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "institution".
 *
 * @property int $id 自增ID
 * @property string $code 编码
 * @property string $name 全称
 * @property string $sort_name 简称
 * @property int $level 层级 1 总公司 2 子公司 3 代理商 4 业务员
 * @property string $parent_id 上级编码
 * @property string $phone 联系电话
 * @property string|null $content 简介
 * @property string|null $image 资质照片
 * @property string|null $status 状态
 * @property string|null $belong_city_list 厅点
 * @property string $create_name 创建人
 * @property string $create_time 创建时间
 * @property string $update_name 更新人
 * @property string $update_time 更新时间
 */
class Institution extends \yii\db\ActiveRecord
{
    const STATUS_CANCEL = 0;
    const STATUS_NORMAL = 1;
    public static $statusList = [
        self::STATUS_NORMAL => '正常',
        self::STATUS_CANCEL => '禁用',
    ];

    const LEVEL_PARENT = 1;
    const LEVEL_SUN = 2;
    const LEVEL_AGENT = 3;
    const LEVEL_SALESMAN = 4;
    public static $levelList = [
        self::LEVEL_PARENT => '总公司',
        self::LEVEL_SUN => '子公司',
        self::LEVEL_AGENT => '代理商',
        self::LEVEL_SALESMAN => '业务员',
    ];


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'institution';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['code', 'required', 'message' => '编码不可以为空'],
            ['code', 'unique', 'targetClass' => '\backend\models\Institution', 'message' => '编码已存在.'],
            ['name', 'required', 'message' => '名称不可以为空'],
            ['name', 'unique', 'targetClass' => '\backend\models\Institution', 'message' => '名称已存在.'],
            ['phone', 'required', 'message' => '联系电话不可以为空'],

            [['level', 'status'], 'integer'],
            [['content', 'image'], 'string'],
            [['create_time', 'update_time', 'belong_city_list'], 'safe'],
            [['code'], 'string', 'max' => 10],
            [['name'], 'string', 'max' => 100],
            [['sort_name', 'parent_id', 'phone', 'create_name', 'update_name'], 'string', 'max' => 20],
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
            'name' => '名称',
            'sort_name' => '简称',
            'level' => '类型',
            'parent_id' => '上级编码',
            'phone' => '联系电话',
            'content' => '简介',
            'image' => '照片',
            'belong_city_list' => '厅点',
            'status' => '状态',
            'create_name' => '创建人用户名',
            'create_time' => '创建时间',
            'update_name' => '修改人用户名',
            'update_time' => '修改时间',
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

    /**
     * 获取状态文字.
     *
     * @param $level
     * @return mixed|string
     */
    public static function getLevelName($level)
    {
        return isset(self::$levelList[$level]) ? self::$levelList[$level] : '无';
    }

    /**
     * @param $level
     */
    public static function getParentsIdListByLevel($level)
    {
        $parentsCodeList = self::find()->where(['level' => $level, 'status' => self::STATUS_NORMAL])->asArray()->all();
        return $parentsCodeList;
    }

    /**
     * @param $level
     */
    public static function getParentsIdListOptionById($id)
    {
        $parentsCodeList = self::find()->where(['parent_id' => $id])->asArray()->all();
        return $parentsCodeList;
    }

    public static function getNameById($id)
    {
        $model = self::findOne($id);
        return isset($model->name) ? $model->name : '';
    }

    public static function getAllById($id, $level)
    {
        if ($level != Institution::LEVEL_PARENT) {
            $res = self::find()->where(['id' => $id])->asArray()->all();
        } else {
            $res = self::find()->where(['status' => self::STATUS_NORMAL])->asArray()->all();
        }
        return $res;
    }

    public static function getLevelList($level)
    {
        $levelList = [];
        foreach (self::$levelList as $key => $value) {
            if ($key == $level) {
                $levelList[$key] = $value;
            }
        }
        return $levelList;
    }

    public static function getBelongCityListById($id)
    {
        $institutionRes = Institution::findOne($id);
        $belongCityIdList = json_decode($institutionRes->belong_city_list, true);
        $query = new Query();
        $query->select('name');
        $query->from(BelongCity::tableName());
        $query->where(['in','id', $belongCityIdList]);
        return $query->column();

    }
     public static function getBelongCityIdListById($id)
    {
        $institutionRes = Institution::findOne($id);
        $belongCityIdList = json_decode($institutionRes->belong_city_list, true);
        $query = new Query();
        $query->select('id');
        $query->from(BelongCity::tableName());
        $query->where(['in','id', $belongCityIdList]);
        return $query->column();

    }

    public static function getParentIdById($id)
    {
        $res = self::find()->where(['id' => $id])->select('parent_id')->asArray()->scalar();
        return $res;
    }
}
