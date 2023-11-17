<?php
namespace common\components;


class KdApi
{
//    public static string $id = '14e1b600b1fd579f47433b88e8d85291';
    public static string $id = '5397c03176c2a4c7a7dae1ab29f79209';
    public static string $com = 'zhaijisong';

    public static function getDeliveryInfo($logisticNo, $logisticCompany)
    {
        $res = [
            'success' => 0,
            'msg' => '',
            'data' => []
        ];
//        $apiUrl = 'http://highapi.kuaidi.com/sandbox-query.html?id=' . self::$id . '&com=' . self::$com . '&nu=' . $logisticNo;
        $apiUrl = 'http://highapi.kuaidi.com/openapi-querycountordernumber.html?id=' . self::$id . '&com=' . $logisticCompany . '&nu=' . $logisticNo;
        try {
            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => $apiUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_HTTPHEADER => [
                    'Cookie: lang=zh-cn; theme=default'
                ],
            ]);
            $response = curl_exec($curl);
            curl_close($curl);
            $resArr = json_decode($response, true);
            if (!empty($resArr)) {
                if (!$resArr['success']) {
                    throw  new \Exception('kuaidi查询错误，原因：' . $resArr['reason'] . '！');
                }
                if (empty($resArr['data'])) {
                    throw  new \Exception('kuaidi查询错误，原因：轨迹信息为空！');
                }
                $res['success'] = 1;
                $res['data'] = $resArr['data'];
            } else {
                throw  new \Exception('kuaidi返回数据解析错误！');
            }

        } catch (\Exception $e) {
            $res['msg'] = $e->getMessage();
        }
        return $res;
    }
}