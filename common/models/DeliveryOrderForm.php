<?php
namespace common\models;

use yii\base\Model;

/**
 * Signup form
 */
class DeliveryOrderForm extends Model
{
    public $id; #自增ID
    public $order_no; #订单号
    public $customer_id; #客户ID
    public $institution_id; #组织机构ID
    public $delivery_no; #物流单号; #系统生成; #系统唯一
    public $logistic_id; #快递公司ID
    public $logistic_no; #快递单号
    public $sec_logistic_id; #第二快递公司ID
    public $sec_logistic_no; #第二快递单号
    public $destination_mark; #大头笔
    public $device_id; #设备ID
    public $send_time; #发货时间-同步
    public $receive_time; #揽收时间-同步取件精灵扫码时间
    public $package_collection_time; #集包时间-取快手扫码时间
    public $allocation_time; #分拨时间
    public $delivered_time; #妥投时间-分析第二快递物流信息
    public $reject_time; #拒收时间-分析第二快递物流信息
    public $estimate_time; #应到时间？？？
    public $receiver_name; #收货人姓名
    public $receiver_company; #收件单位
    public $receiver_phone; #收货人电话
    public $receiver_address; #收货地址
    public $sender_name; #寄件人姓名
    public $sender_phone; #寄件人联系电话
    public $sender_company; #寄件人单位
    public $sender_address; #寄件人地址
    public $taker_name; #取件人姓名
    public $taker_code; #取件人编码
    public $taker_company; #取件人公司（厅点）
    public $weight; #重量KG
    public $volume; #体积
    public $long; #长CM
    public $wide; #宽CM
    public $high; #高CM
    public $province; #省
    public $city; #市
    public $district; #区/县
    public $towns; #乡/镇
    public $village; #村/社区/居委会
    public $status; #状态; #01; #发货（预留）; #02; #揽收（预留）; #03; #集包; #04; #分拨; #05; #妥投; #06; #拒收
    public $latest_track_info; #最后一条配送信息
    public $is_delay; #延误标签; #0; #否; #1; #是; #妥投时间大于应到时间
    public $is_agent_settle; #代理商支付标签; #0; #否; #1; #是; #代理商结算单为已完成状态
    public $is_customer_settle; #供应商支付标签; #0; #否; #1; #是供应商结算单为已完成状态
    public $is_logistic_company_settle; #快递公司支付标签; #0; #否; #1; #是
    public $truck_classes_no; #卡车班次
    public $order_total_price; #订单总金额
    public $total_price; #应付总金额（订单总金额减去折扣加上运费）
    public $create_name; #创建人用户名
    public $create_time; #创建时间
    public $update_name; #更新人用户名
    public $update_time; #更新时间

    /**
     * @inheritdoc
     * 对数据的校验规则
     */
    public function rules()
    {
        return [
            ['logistic_no', 'required', 'message' => '快递单号不可以为空'],
            ['weight', 'required', 'message' => '重量不可以为空'],
            ['device_id', 'required', 'message' => '设备ID不可以为空'],
            ['receiver_name', 'required', 'message' => '收货人姓名不可以为空'],
            ['receiver_phone', 'required', 'message' => '收货人电话不可以为空'],
            ['logistic_no', 'unique', 'targetClass' => '\common\models\DeliveryOrder', 'message' => '快递单号已存在.'],
            ['delivery_no', 'unique', 'targetClass' => '\common\models\DeliveryOrder', 'message' => '运单号已存在.'],

        ];
    }
}


