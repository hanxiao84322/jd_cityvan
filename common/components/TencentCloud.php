<?php
namespace common\components;

use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Ocr\V20181119\Models\GeneralAccurateOCRRequest;
use TencentCloud\Ocr\V20181119\OcrClient;
class TencentCloud {
    public static function RecognizeTableAccurateOCR($image = '')
    {
        try {
            $cred = new Credential("AKIDvmWDhvvfFerqbFjALK5bikdJm01oPkqq", "BFAuRmtmPp8JVbXEenjS0WYWUFl0A9zb");
            $httpProfile = new HttpProfile();
            $httpProfile->setEndpoint("ocr.tencentcloudapi.com");

            $clientProfile = new ClientProfile();
            $clientProfile->setHttpProfile($httpProfile);
            $client = new OcrClient($cred, "ap-beijing", $clientProfile);

            // 实例化一个请求对象,每个接口都会对应一个request对象
            $req = new GeneralAccurateOCRRequest();

            $params = array(
                "ImageBase64" => $image
            );
            $req->fromJsonString(json_encode($params));

            // 返回的resp是一个GeneralAccurateOCRResponse的实例，与请求对象对应
            $resp = $client->RecognizeTableAccurateOCR($req);

            // 输出json格式的字符串回包
            return $resp->toJsonString();
        }
        catch(TencentCloudSDKException $e) {
            echo $e;
        }
    }

    /**
     * @param $image
     * @return array
     */
    public static function analysisOcrData($image)
    {
        $return = [
            'success' => false,
            'msg' => '',
            'data' => [
                'name' => '',
                'phone' => '',
                'text' => ''
            ]
        ];
        $logisticNo = '';
        $name = '';
        $phone = '';
        $extPhone = '';
        $textList = [];
        $text = '';
        try {
            if (!empty($image)) {
                $orcRes = TencentCloud::RecognizeTableAccurateOCR($image);
                $orcRes = json_decode($orcRes, true);
                $tableDetections = isset($orcRes['TableDetections']) ? $orcRes['TableDetections'] : '';
                if (is_array($tableDetections) && !empty($tableDetections)) {
                    foreach ($tableDetections as $tableDetection) {
                        foreach ($tableDetection as $value) {
                            if (!empty($value) && is_array($value)) {
                                foreach ($value as $item) {
                                    if (!empty($item) && is_array($item) && isset($item['Text'])) {
                                        $textList[] = $item['Text'];
                                    }
                                }
                            }
                        }
                    }

                    $text = implode('|', $textList);
                    $text = str_replace(PHP_EOL, '|', $text);

                    $text = str_replace(' ', '|', $text);
                    $logisticNo = Utility::getLogisticNo($text);
                    $phone = Utility::getPhone($text);
                    $extPhone = Utility::getExtPhone($text);
                    $name = Utility::getName($text, $phone);

                    if (!empty($extPhone)) {
                        $nameIsExtPhone = Utility::getExtPhone($name);
                        if (empty($nameIsExtPhone)) {
                            $name .= $extPhone;
                        }
                    }
                } else {
                    throw new \Exception('图片识别失败，解析orc返回结果失败，没有TextDetections。');
                }

            }
            $return['success'] = true;
            $return['data'] = [
                'logisticNo' => $logisticNo,
                'name' => $name,
                'phone' => $phone,
                'text' => $text,
                'extPhone' => $extPhone
            ];
        } catch (\Exception $e) {
            $return['msg'] = $e->getMessage();
        }
        return $return;

    }

    public static function isSenderInfo($deviceReceiverName, $senderName, $deviceReceiverPhone, $senderPhone, $extPhone)
    {
        if ((mb_substr($deviceReceiverName,0,1,'utf8') == mb_substr($senderName,0,1,'utf8')) && (substr($deviceReceiverPhone,-4) == substr($senderPhone,-4)) && $extPhone == '') {
            return true;
        }
        return false;
    }
}
