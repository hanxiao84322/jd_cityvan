<?php

namespace common\components;


class EmsCloud {
    public static function getDeliveryInfo($orderNo)
    {
        $get_content = '';
        $url = 'http://211.156.193.140:8000/cotrackapi/api/track/mail/' . $orderNo;
        $header[] = "version:ems_track_cn_1.0";
        $header[] = "authenticate:shandongems_zd3fcq8jv2cvw4hsk";
        $curl_option = array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => $header
        );
        try {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            if (is_array($curl_option) && !empty($curl_option)) {
                foreach ($curl_option as $key => $val) {
                    curl_setopt($curl, $key, $val);
                }
            }
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($curl, CURLOPT_TIMEOUT, 10);

            $get_content = curl_exec($curl);
            if (curl_error($curl)) {
                echo curl_error($curl);
            }
            curl_close($curl);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        return json_decode($get_content, true);
    }
}
