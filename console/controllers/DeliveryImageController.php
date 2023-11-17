<?php


namespace console\controllers;


use common\models\LogisticImage;
use yii\console\Controller;

class DeliveryImageController extends Controller
{
    /**
     * ./yii delivery-image/batch-create-by-images
     */
    public function actionBatchCreateByImages()
    {
        $folderPath = '2023-08-31'; // 替换为实际的文件夹路径

// 获取文件夹中的图片文件列表
        $imageFiles = glob($folderPath . '/*.jpg');

        foreach ($imageFiles as $imageFile) {
            try {

                // 获取文件名（不包括扩展名）
                $filename = pathinfo($imageFile, PATHINFO_FILENAME);

                // 将图片文件转换为Base64字符串
                $imageData = base64_encode(file_get_contents($imageFile));
                $logisticImageExists = LogisticImage::find()->where(['logistic_no' => $filename])->exists();
                if (!$logisticImageExists) {
                    $deliveryImage = new LogisticImage();
                    $deliveryImage->device_id = 'DWS007';
                    $deliveryImage->logistic_no = $filename;
                    $deliveryImage->image_base64_str = $imageData;
                    $deliveryImage->create_time = date('Y-m-d H:i:s', time());
                    if (!$deliveryImage->save()) {
                        throw new \Exception("快递单号：" . $filename . "保存失败，原因：" . $deliveryImage->getErrors());
                    }
                }
                echo "logistic_no:" . $filename . " create success!\r\n";
            } catch (\Exception $e) {
                echo $e->getMessage() . "\r\n";
            }
//            // 输出文件名和Base64字符串
//            echo '文件名：' . $filename . PHP_EOL;
//            echo 'Base64字符串：' . $imageData . PHP_EOL . PHP_EOL;
        }
    }
}