<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "logistic_company_fee_rules".
 *
 * @property int $id
 * @property string $warehouse_code 仓库编码
 * @property int $logistic_id 快递公司 ID
 * @property string $province 省
 * @property string $city 市
 * @property string $district 区县
 * @property string $province_code 省
 * @property string $city_code 市
 * @property string $district_code 区县
 * @property float $weight 首重公斤
 * @property int $weight_round_rule 首重取整规则
 * @property float $price 首重价格元
 * @property string $continue_weight_rule 续重规则
 * @property int $continue_weight_round_rule 续重取整规则
 * @property string $create_username 创建人用户名
 * @property string $create_time 创建时间
 * @property string $update_username 更新人用户名
 * @property string $update_time 更新时间
 */
class LogisticCompanyFeeRules extends \yii\db\ActiveRecord
{
    public $logistic_company_name;

    const WEIGHT_ROUND_RULE_NOT_UP = 1;
    const WEIGHT_ROUND_RULE_HALF_UP = 2;
    const WEIGHT_ROUND_RULE_UP = 3;

    public static $weightRoundRuleList = [
        self::WEIGHT_ROUND_RULE_HALF_UP => '四舍五入',
        self::WEIGHT_ROUND_RULE_NOT_UP => '只舍不入',
        self::WEIGHT_ROUND_RULE_UP => '全入不舍',
    ];


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'logistic_company_fee_rules';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['warehouse_code', 'logistic_id', 'province_code', 'weight', 'weight_round_rule', 'price', 'continue_weight_rule'], 'required'],
            [['logistic_id', 'weight_round_rule', 'continue_weight_round_rule'], 'integer'],
            [['weight', 'price'], 'number'],
            [['continue_weight_rule'], 'string'],
            [['create_time', 'update_time'], 'safe'],
            [['warehouse_code'], 'string', 'max' => 20],
            [['province', 'city', 'district', 'province_code', 'city_code', 'district_code', 'create_username', 'update_username'], 'string', 'max' => 50],
            [['warehouse_code', 'logistic_id', 'province', 'city', 'district'], 'unique', 'targetAttribute' => ['warehouse_code', 'logistic_id', 'province', 'city', 'district']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'warehouse_code' => '发货仓',
            'logistic_id' => '快递公司',
            'logistic_company_name' => '快递公司',
            'province' => '省',
            'city' => '市',
            'district' => '区县',
            'province_code' => '省',
            'city_code' => '市',
            'district_code' => '区县',
            'weight' => '首重',
            'weight_round_rule' => '首重取整规则',
            'price' => '首重价格',
            'continue_weight_rule' => '续重规则',
            'continue_weight_round_rule' => '续重取整规则',
            'create_username' => '创建人用户名',
            'create_time' => '创建时间',
            'update_username' => '更新人用户名',
            'update_time' => '更新时间',
        ];
    }

    public static function getContinueWeightRoundRuleText($continueWeightRoundRule)
    {
        $continueWeightRoundRuleText = '';
        $continueWeightRoundRuleArr = json_decode($continueWeightRoundRule, true);
        foreach ($continueWeightRoundRuleArr as $item) {
            $continueWeightRoundRuleText .= implode(',', $item) . "\r\n";
        }

        return $continueWeightRoundRuleText;
    }

    public static function getContinueWeightRoundRuleView($continueWeightRoundRule) {
        $continueWeightRoundRuleText = '';
        $continueWeightRoundRuleArr = json_decode($continueWeightRoundRule, true);

        foreach ($continueWeightRoundRuleArr as $key => $item) {
            if (!empty($item[0])) {
                if (empty($item[1])) {
                    $continueWeightRoundRuleText .= "第" . $key + 1 . "阶-大于:" . $item[0] .",价格(元):" . $item['2'] . "<br>";
                } else {
                    $continueWeightRoundRuleText .= "第" . $key + 1 . "阶-起始重量:" . $item[0] . ",结束重量:" . $item['1'] . ",价格(元):" . $item['2'] . "<br>";

                }
            }

        }

        return $continueWeightRoundRuleText;
    }

    public static function getWeightRoundRule($WeightRoundRule)
    {
        return isset(self::$weightRoundRuleList[$WeightRoundRule]) ? self::$weightRoundRuleList[$WeightRoundRule] : '无';
    }


}
