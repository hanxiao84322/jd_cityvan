<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "logistic_company_timeliness".
 *
 * @property int $id
 * @property string $warehouse_code 仓库编码
 * @property int $logistic_id 快递公司 ID
 * @property string $province 省
 * @property string $province_code 省
 * @property string $city 市
 * @property string $city_code 市
 * @property string $district 区县
 * @property string $district_code 区县
 * @property int $timeliness  时效（天）
 */
class LogisticCompanyTimeliness extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'logistic_company_timeliness';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['warehouse_code', 'logistic_id', 'province', 'timeliness'], 'required'],
            [['logistic_id', 'timeliness'], 'integer'],
            [['warehouse_code', 'province', 'city', 'district', 'province_code', 'city_code', 'district_code'], 'string', 'max' => 20],
            [['warehouse_code', 'logistic_id', 'province', 'city', 'district'], 'unique', 'targetAttribute' => ['warehouse_code', 'logistic_id', 'province', 'city', 'district']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'warehouse_code' => '仓库编码',
            'logistic_id' => '快递公司 ID',
            'province' => '省',
            'province_code' => '省',
            'city' => '市',
            'city_code' => '市',
            'district' => '区/县',
            'district_code' => '区/县',
            'timeliness' => '时效（天）',
        ];
    }

    public static function getTimelinessByDeliveryOrderInfo($warehouseCode, $logisticId, $province, $city, $district)
    {
        $logisticCompanyTimeliness = LogisticCompanyTimeliness::find()->select('timeliness')->where(['warehouse_code' => $warehouseCode, 'logistic_id' => $logisticId, 'province' => $province, 'city' => $city, 'district' => $district])->scalar();
        if (empty($logisticCompanyTimeliness)) {
            $logisticCompanyTimeliness = LogisticCompanyTimeliness::find()->select('timeliness')->where(['warehouse_code' => $warehouseCode, 'logistic_id' => $logisticId, 'province' => $province, 'city' => $city, 'district' => ''])->scalar();
            if (empty($logisticCompanyTimeliness)) {
                $logisticCompanyTimeliness = LogisticCompanyTimeliness::find()->select('timeliness')->where(['warehouse_code' => $warehouseCode, 'logistic_id' => $logisticId, 'province' => $province, 'city' => '', 'district' => ''])->scalar();
            }
        }
        return $logisticCompanyTimeliness;

    }

}
