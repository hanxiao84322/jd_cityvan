<?php
namespace common\components;

class ZtoCloud {
    public static function getDeliveryInfo($orderNo)
    {
        $orderNo = '78365275168226';
        $key = 'C47527C3F2DD263FF46FE86FAAB35294';
        $url = 'http://japi.zto.cn/zto/api_utf8/traceInterface';
        $company_id = '9010f8a380d44f77b2032808bc8bd445';
        $request_data = json_encode(array($orderNo));
        $data_digest = MD5($request_data.$key);
        $url = $url . "?data=" . $request_data . "&data_digest=" . $data_digest . "&msg_type=TRACES&company_id=".$company_id;
        return self::get_content($url);
    }

    public static function get_content($url)
    {
        $return = [
            'success' => 0,
            'msg' =>  '',
            'data' => ''
        ];
        try {
            $opts = array(
                'http' => array(
                    'method' => "GET",
                    'timeout' => 10, //设置超时
                )
            );
            $context = stream_context_create($opts);
            $get_content = file_get_contents($url, false, $context);
            $return['success']  = 1;
            $return['data'] = $get_content;
        } catch (\Exception $e) {
            $return['msg'] = $e->getMessage();
            $e->getMessage();
        }
        return $return;
    }
}