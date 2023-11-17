<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "zjs_delivery_info".
 *
 * @property int $id
 * @property string|null $client_flag 客户标识
 * @property string|null $mail_no 运单号
 * @property string|null $order_no 客户单号
 * @property string|null $contacter 派送人
 * @property string|null $contact_phone 派件员联系方式
 * @property string|null $time 操作时间
 * @property string|null $desc 跟踪信息描述
 * @property string|null $action 事件/操作
 * @property string|null $city 当前城市
 * @property string|null $facility_type 站点类型
 * @property string|null $facility_no 操作站点编号
 * @property string|null $facility_name 操作站点名称
 * @property string|null $tz 时区
 * @property string|null $next_city 下站到达城市
 * @property string|null $next_node_type 下站到达节点类型
 * @property string|null $next_node_code 下站到达节点编码
 * @property string|null $next_node_name 下站达到节点名称
 * @property string|null $country 国家
 * @property string|null $next_mail_no 转件编号
 * @property string|null $next_source_name 转件资源名称
 * @property string|null $signer 签收人
 * @property string|null $extended_field 扩展字段
 * @property string|null $package_num 包裹件数
 * @property string|null $weight 计费重量
 * @property string|null $exception_code 异常编码
 * @property string|null $exception_desc 异常描述
 * @property string|null $remark 备注信息
 * @property string|null $create_time 新建时间
 */
class ZjsDeliveryInfo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'zjs_delivery_info';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['extended_field', 'remark'], 'string'],
            [['client_flag', 'mail_no', 'order_no', 'contacter', 'contact_phone', 'time','create_time', 'action', 'city', 'facility_type', 'facility_no', 'facility_name', 'tz', 'next_city', 'next_node_type', 'next_node_code', 'next_node_name', 'country', 'next_mail_no', 'next_source_name', 'signer', 'exception_code', 'exception_desc'], 'string', 'max' => 64],
            [['desc'], 'string', 'max' => 500],
            [['package_num', 'weight'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_flag' => 'Client Flag',
            'mail_no' => 'Mail No',
            'order_no' => 'Order No',
            'contacter' => 'Contacter',
            'contact_phone' => 'Contact Phone',
            'time' => 'Time',
            'desc' => 'Desc',
            'action' => 'Action',
            'city' => 'City',
            'facility_type' => 'Facility Type',
            'facility_no' => 'Facility No',
            'facility_name' => 'Facility Name',
            'tz' => 'Tz',
            'next_city' => 'Next City',
            'next_node_type' => 'Next Node Type',
            'next_node_code' => 'Next Node Code',
            'next_node_name' => 'Next Node Name',
            'country' => 'Country',
            'next_mail_no' => 'Next Mail No',
            'next_source_name' => 'Next Source Name',
            'signer' => 'Signer',
            'extended_field' => 'Extended Field',
            'package_num' => 'Package Num',
            'weight' => 'Weight',
            'exception_code' => 'Exception Code',
            'exception_desc' => 'Exception Desc',
            'remark' => 'Remark',
        ];
    }
}
