<?php

namespace common\helpers;

use yii\base\Exception;
use yii\helpers\ArrayHelper;

require_once \Yii::$app->basePath.'/../common/components/phpexcel/PHPExcel.php';

class ExcelHelper
{
    public $file = null;

    public static function load($filename = "")
    {
        $model = new self();
        $model->file = \PHPExcel_IOFactory::load($filename);
        return $model;
    }

    /**
     * @return mixed
     */
    public function toArray()
    {
        /** @var \PHPExcel_Worksheet $activeSheet */
        $activeSheet = $this->file->getActiveSheet();

        $array = [];
        $highestRow = $activeSheet->getHighestRow();
        $highestColumn = $activeSheet->getHighestColumn();
        $highestColumnNum = ord($highestColumn) - ord('A');
        for ($i = 2; $i <= $highestRow; $i++) {
            for ($j = 0; $j <= $highestColumnNum; $j++) {
                $array[$i][] = $activeSheet->getCellByColumnAndRow($j, $i)->getFormattedValue();
            }
        }
        if (!empty($array)) {
            return array_values($array);
        } else {
            return [];
        }
    }

    /**
     * 把返回的数组对应为指定key的数组.
     *
     *  $arr = [
     *      'partner_id' => 0,
     *      'category_id' => 1,
     *      'brand_id' => 2,
     *      'mall_rate' => 5,
     *  ];
     *
     * @param array $map
     *
     * @return mixed
     *
     */
    public function toFormatArray($map)
    {
        //定义返回数组
        $newArr = [];
        //excel上传数据的数组
        $data = $this->toArray();
        $count = count($data);
        for ($i = 0; $i < $count; $i++) {
            foreach ($map as $key => $value) {
                $newArr[$i][$key] = trim(ArrayHelper::getValue($data[$i],$value,''));
            }
        }
        return $newArr;
    }

    /**
     *
     *
     * @param        $path
     * @param string $title
     * @param string $filename
     * @param array  $textColumns 需要转换为文本格式的列.
     * @param string $type
     * @return bool
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Writer_Exception
     */
    public static function csvToExcel($path, $title = "数据", $filename = "data", $textColumns = [], $type = 'download')
    {
        require_once(\Yii::getAlias("@common")."/components/phpexcel/PHPExcel.php");

        if (!file_exists($path)){
            return false;
        }
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objActiveSheet = $objPHPExcel->getActiveSheet();
        $csv = fopen($path, 'r');
        $rowNum = 1;
        while ($row = fgetcsv($csv)) {
            $columnNum = 0;
            foreach ($row as $value) {
                $columnCode = \PHPExcel_Cell::stringFromColumnIndex($columnNum);
                $needString = false;
                if (in_array(strtolower($columnCode), $textColumns) || in_array('all', $textColumns)) {
                    $needString = true;
                }
                if ($rowNum > 1 && $needString) {
                    $objActiveSheet->setCellValueExplicit($columnCode . $rowNum, $value, \PHPExcel_Cell_DataType::TYPE_STRING);
                } else {
                    $objActiveSheet->setCellValue($columnCode . $rowNum, $value);
                }
                $columnNum++;
            }
            $rowNum++;
        }
        $objActiveSheet->setTitle($title);

        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        if($type == 'download') {
            header("Pragma:public");
            header("Content-Type:application/x-msexecl;name=".$filename);
            header("Content-Disposition:inline;filename=".$filename);
            $objWriter->save("php://output");
            exit();
        } else {
            $excelPath = str_replace('.csv', '.xlsx', $path);
            $objWriter->save($excelPath);
            return true;
        }
    }

    /**
     * 下载excel文件
     *
     * @param        $path
     * @param string $fileName
     */
    public static function downloadExcel($path, $fileName = '')
    {
        $file = fopen($path, 'r');
        $fileSize = filesize($path);
        Header("Content-type:application/octet-stream");
        Header("Accept-Ranges:bytes");
        Header("Accept-Length:" . $fileSize);
        header("Content-Type: application/msexcel");
        Header("Content-Disposition:attachment;filename=" . $fileName);
        echo fread($file, $fileSize);
        fclose($file);
        exit();
    }

    /**
     * 写入追加Excel.
     *
     * @param $fileName
     * @param $headerList
     * @param $dataList
     */
    public static function writeExcel($fileName, $headerList, $dataList)
    {
        if (file_exists($fileName)) {
            $objReader = new \PHPExcel_Reader_Excel2007();
            $objPHPExcel = $objReader->load($fileName);
            $sheet = $objPHPExcel->getSheet(0);
            // 取得最大行
            $highestRow = $sheet->getHighestRow();
            // 追加开始行
            $row = $highestRow + 1;
        } else {
            // 新建excel
            $objPHPExcel = new \PHPExcel();
            $sheet = $objPHPExcel->getSheet(0);

            // 设置表头
            $key = ord("A");
            foreach ($headerList as $val) {
                if ($key > ord('Z')) {
                    $column = 'A' . chr($key - 26);
                } else {
                    $column = chr($key);
                }
                $sheet->setCellValue($column . '1', $val);
                $key++;
            }

            // 新建开始行
            $row = 2;
        }

        // 写入数据
        foreach ($dataList as $rows) {
            $col = ord("A");
            foreach ($rows as $value) {
                if ($col > ord('Z')) {
                    $column = 'A' . chr($col - 26);
                } else {
                    $column = chr($col);
                }
                $sheet->setCellValue($column . $row, $value);
                $col++;
            }
            $row++;
        }

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($fileName);

        unset($objWriter);
        unset($sheet);
        unset($objPHPExcel);
    }

    /**
     * 写入追加csv.
     *
     * @param $fileName
     * @param $headerList
     * @param $dataList
     */
    public static function writeCsv($fileName, $headerList, $dataList)
    {
        if (file_exists($fileName)) {
            $file = fopen($fileName, 'a');
        } else {
            $file = fopen($fileName, 'a');
            fputcsv($file, $headerList);
        }

        foreach ($dataList as $item) {
            fputcsv($file, $item);
        }

        fclose($file);
    }

}
