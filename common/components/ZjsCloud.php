<?php
namespace common\components;

use common\models\ZjsApiCount;

class ZjsCloud {
    public static string $clientFlag = 'zysk';
    // public static string $secretKey = '8db4b8bf11596e1a947460552358c9ee';//测试
    public static string $secretKey = '2fae9a50e630f20408422429a8f1d635';//正式

    /**
     * 查询物流轨迹接口
     *
     * @param $logisticNo
     * @return array|mixed
     * @throws \Exception
     */
    public static function getDeliveryInfo($logisticNo)
    {
        // $apiUrl = 'http://businesstest.zjs.com.cn:8001/interface/iwc/querystatus'; //测试
        $apiUrl = 'http://business.zjs.com.cn:9200/interface/prev/querystatus'; //正式
        $businessData = [
            'clientFlag' => self::$clientFlag,
            'orders' => [['mailNo' => $logisticNo]]
        ];
        $businessData = json_encode($businessData);
        $verifyData = self::getVerifyData(self::$clientFlag, $businessData, self::$secretKey);
        $requestData = [
            'clientFlag' => self::$clientFlag,
            'verifyData' => $verifyData,
            'data' => $businessData
        ];
        $res = Utility::curlGetContents($apiUrl, 'POST', $requestData);
        $zjsApiCountModel = new ZjsApiCount();
        $zjsApiCountModel->logistic_no = $logisticNo;
        $zjsApiCountModel->count = 1;
        $zjsApiCountModel->create_time = date('Y-m-d H:i:s', time());
        if (!$zjsApiCountModel->save()) {
        $res['success'] = 0;
        $res['msg'] = Utility::arrayToString($zjsApiCountModel->getErrors());
    }
        if ($res['success']) {
            $data = json_decode($res['data'], true);
            if (isset($data['state']) && $data['state'] != '200') {
                $res['success'] = 0;
                $res['msg'] = $data['reason'];
            }
        }
        return $res;
    }

    static function getVerifyData($clientFlag, $strData, $secretKey)
    {
        $ran1 = '5097';
        $ran2 = '2995';
        $str = 'z宅J急S送g';
        $str = $ran1 . $clientFlag . $strData . $secretKey . $str . $ran2;
        $str = strtolower(md5($str));
        $md5str = substr($str, 7,21);
        $verifyData = $ran1 . $md5str . $ran2;
        return $verifyData;
    }

    /**
     * 订阅推送物流轨迹接口
     *
     * @param $logisticNo
     * @return array|mixed
     * @throws \Exception
     */
    public static function getSubscribe($logisticNo)
    {
        // $apiUrl = 'http://businesstest.zjs.com.cn:8001/interface/prev/savebatchediwccc';//测试
        $apiUrl = 'http://business.zjs.com.cn:9200/interface/prev/savebatchediwccc';//正式
        $businessData[] = [
            'clientFlag' => self::$clientFlag,
            'mailNo' => $logisticNo
        ];
        $businessData = json_encode($businessData);
        $verifyData = self::getVerifyData(self::$clientFlag, $businessData, self::$secretKey);
        $requestData = [
            'clientFlag' => self::$clientFlag,
            'verifyData' => $verifyData,
            'data' => $businessData
        ];
        $res = Utility::curlGetContents($apiUrl, 'POST', $requestData);
        return $res;

    }
}