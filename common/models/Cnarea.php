<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "cnarea_2020".
 *
 * @property int $id
 * @property int $level 层级
 * @property int $parent_code 父级行政代码
 * @property int $area_code 行政代码
 * @property int $zip_code 邮政编码
 * @property string $city_code 区号
 * @property string $name 名称
 * @property string $short_name 简称
 * @property string $merger_name 组合名
 * @property string $pinyin 拼音
 * @property float $lng 经度
 * @property float $lat 纬度
 */
class Cnarea extends \yii\db\ActiveRecord
{
    const LEVEL_ONE = 0;
    const LEVEL_TWO = 1;
    const LEVEL_THREE = 2;
    const LEVEL_FOUR = 3;
    const LEVEL_FIVE = 4;
    public static $levelList = [
        self::LEVEL_ONE => '一级(省/直辖市)',
        self::LEVEL_TWO => '二级(市)',
        self::LEVEL_THREE => '三级(区/县)',
        self::LEVEL_FOUR => '四级(乡/镇)',
        self::LEVEL_FIVE => '五级(村/社区/居委会)',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cnarea_2020';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['level'], 'required'],
            [['level', 'parent_code', 'area_code', 'zip_code'], 'integer'],
            [['lng', 'lat'], 'number'],
            [['city_code'], 'string', 'max' => 6],
            [['name', 'short_name', 'merger_name'], 'string', 'max' => 50],
            [['pinyin'], 'string', 'max' => 30],
            [['area_code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'level' => '等级',
            'parent_code' => '上级编码',
            'area_code' => '编码',
            'zip_code' => '邮编',
            'city_code' => '城市编码',
            'name' => '名称',
            'short_name' => '简称',
            'merger_name' => 'Merger Name',
            'pinyin' => '拼音',
            'lng' => '横坐标',
            'lat' => '纵坐标',
        ];
    }

    public static function getAllByLevel($level)
    {
        $date = self::find()->where(['level' => $level])->asArray()->all();
        return $date;
    }

    public static function getAllByParentCode($parentCode)
    {
        $date = self::find()->where(['parent_code' => $parentCode])->asArray()->all();
        return $date;
    }

    public static function getNameByCode($areaCode)
    {
        if (!empty($areaCode)) {
            $date = self::find()->where(['area_code' => $areaCode])->asArray()->one();
            return $date['name'];
        } else {
            return '';
        }

    }

    public static function getCodeByName($areaName)
    {
        if (!empty($areaName)) {
            $data = self::find()->where(['name' => $areaName])->asArray()->one();
            if (!empty($data)) {
                return $data['area_code'];
            }
        } else {
            return '';
        }

    }

    public static function getParentNameByName($areaName, $level)
    {

        $parentName = '';
        $parentCode = self::find()->select('parent_code')->where(['name' => $areaName, 'level' => $level])->andWhere('merger_name like "%四川%" or merger_name like "%青海%" or merger_name like "%西藏%" or merger_name like "%甘肃%"')->scalar();
        if (!empty($parentCode)) {
            $parentName = self::find()->select('name')->where(['area_code' => $parentCode])->scalar();
        }
        return $parentName;
    }
}
