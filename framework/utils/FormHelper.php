<?php

namespace app\framework\utils;

include_once "xlsxwriter.class.php";

class FormHelper
{

    public static function allowString($string)
    {
        return trim($string);
    }

    public static function outPut($outputFields, $list, $downloadPath, $filename, $numFormat = ['G' => \PHPExcel_Style_NumberFormat::FORMAT_TEXT])
    {
        $cacheMethod = \PHPExcel_CachedObjectStorageFactory::cache_in_memory_serialized;
        $cacheSettings = ['memoryCacheSize'=>'256MB'];
        \PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);



        $index = 1;
        $startColumn = chr(64 + $index) . "1";
        foreach ($outputFields as $key => $val) {
            $ceilName = chr(64 + $index) . "1";
            $ceilVal = $val;
            $objPHPExcel->getActiveSheet()->setCellValue($ceilName, $ceilVal);
            $objPHPExcel->getActiveSheet()->getColumnDimension(chr(65 + $index))->setWidth(20);
            $objPHPExcel->getActiveSheet()->getStyle($ceilName)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $index++;
        }
        $endColumn = chr(64 + count($outputFields)) . "1";

        $objPHPExcel->getActiveSheet()->getStyle($startColumn . ':' . $endColumn)->applyFromArray(
            [
                'font' => ['bold' => true],
                'borders' => [
                    'top' => ['style' => \PHPExcel_Style_Border::BORDER_THIN]
                ],
            ]
        );

        $count = count($list);

        $index = 2;
        for ($i=0; $i<count($list); $i++) {
            $ceilIndex = 1;
            foreach ($outputFields as $k => $v) {
                $ceilName = chr(64 + $ceilIndex) . $index;
                $ceilVal = $list[$i][$k];
                is_array($ceilVal) && $ceilVal = implode("\n", $ceilVal);
                $objPHPExcel->getActiveSheet()->setCellValue($ceilName, "\t".$ceilVal);
                if ($count<=5000) {
                    $objPHPExcel->getActiveSheet()->getStyle($ceilName)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                }

                $ceilIndex++;
            }
            $index++;
        }

        unset($list);
        
        /*
        foreach ($numFormat as $column => $style) {
            $startNum = $column . '2';
            $endNum = $column . count($outputFields);
           $objPHPExcel->getActiveSheet()->getStyle($startNum . ':' . $endNum)->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
        }
         */


        // Save Excel 95 file
        $callStartTime = microtime(true);
        //xls文件名改为xlsx文件名导出
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if ($ext == "xls") {
            $filename = $filename.'x';
        }

        ob_end_clean();
        header('Set-Cookie: fileDownload=true; path=/'); //添加此cookie便于jquery.fileDownload.js判断下载状态
        header("Content-type: text/html; charset=utf-8");
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');

        $encoded_filename = urlencode($filename);
        $ua = $_SERVER["HTTP_USER_AGENT"];
        if (preg_match("/MSIE/", $ua)) {
            Header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
        } elseif (preg_match("/Firefox/", $ua)) {
            Header('Content-Disposition: attachment; filename*="utf8\'\'' . $filename . '"');
        } else {
            Header("Content-Disposition: attachment; filename=" . $filename);
        }

        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');

        exit();
    }

    public static function outPutByXls($outputFields, $list, $filename)
    {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if ($ext == "xls") {
            $filename = \XLSXWriter::sanitize_filename($filename).'x';
        } else {
            $filename = \XLSXWriter::sanitize_filename($filename);
        }
        ob_end_clean();
        header('Content-disposition: attachment; filename="'.$filename.'"');
        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8");
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        $header = [];
        $data = [];
        foreach ($outputFields as $key => $val) {
            $header[$val] = "string";
        }

        for ($i=0; $i<count($list); $i++) {
            $row = [];
            foreach ($outputFields as $k => $v) {
                $ceilVal = $list[$i][$k];
                is_array($ceilVal) && $ceilVal = implode(";", $ceilVal);
                $row[] = $ceilVal === null ? '' : $ceilVal;
            }
            $data[] = $row;
        }

        $writer = new \XLSXWriter();
        $writer->setAuthor('mysoft');
        $writer->writeSheet($data,'Sheet1',$header);
        $writer->writeToStdOut();
        exit(0);
    }

    public static function writeFileByXls($outputFields, $list, $filename)
    {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if ($ext == "xls") {
            $filename = \XLSXWriter::sanitize_filename($filename).'x';
        } else {
            $filename = \XLSXWriter::sanitize_filename($filename);
        }

        $downloadPath = \yii::getAlias("@runtime" . "/download/");
        if (!file_exists($downloadPath)) {
            mkdir($downloadPath, "0755", true);
        }

        $header = [];
        $data = [];
        foreach ($outputFields as $key => $val) {
            $header[$val] = "string";
        }

        for ($i=0; $i<count($list); $i++) {
            $row = [];
            foreach ($outputFields as $k => $v) {
                $ceilVal = $list[$i][$k];
                is_array($ceilVal) && $ceilVal = implode(";", $ceilVal);
                $row[] = $ceilVal;
            }
            $data[] = $row;
        }

        $writer = new \XLSXWriter();
        $writer->setAuthor('mysoft');
        $writer->writeSheet($data,'Sheet1',$header);
        $writer->writeToFile($downloadPath.$filename);
    }

    public static function outPutByDrop($outputFields, $outputDrops, $list, $filename, $numFormat = ['G' => \PHPExcel_Style_NumberFormat::FORMAT_TEXT])
    {
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $index = 1;
        $startColumn = chr(64 + $index) . "1";
        foreach ($outputFields as $key => $val) {
            $ceilName = chr(64 + $index) . "1";
            $ceilVal = $val;
            $objPHPExcel->getActiveSheet()->setCellValue($ceilName, $ceilVal);
            $objPHPExcel->getActiveSheet()->getColumnDimension(chr(65 + $index))->setWidth(20);
            $objPHPExcel->getActiveSheet()->getStyle($ceilName)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $index++;
        }
        $endColumn = chr(64 + count($outputFields)) . "1";

        $objPHPExcel->getActiveSheet()->getStyle($startColumn . ':' . $endColumn)->applyFromArray(
            [
                'font' => ['bold' => true],
                'borders' => [
                    'top' => ['style' => \PHPExcel_Style_Border::BORDER_THIN]
                ],
            ]
        );

        $index = 2;
        foreach ($list as $fields) {
            $ceilIndex = 1;
            foreach ($outputFields as $k => $v) {
                $ceilName = chr(64 + $ceilIndex) . $index;
                $ceilVal = $fields[$k];

                foreach ($outputDrops as $colname => $drops) {
                    if ($k == $colname) {
                        $objPHPExcel->getActiveSheet()->getCell($ceilName)->getDataValidation()
                            -> setType(\PHPExcel_Cell_DataValidation::TYPE_LIST)
                            -> setErrorStyle(\PHPExcel_Cell_DataValidation::STYLE_INFORMATION)
                            -> setAllowBlank(false)
                            -> setShowInputMessage(true)
                            -> setShowErrorMessage(true)
                            -> setShowDropDown(true)
                            -> setErrorTitle('请选择'.$ceilVal)
                            -> setError('您输入的值不在下拉框列表内.')
                            -> setPromptTitle($ceilVal)
                            -> setFormula1('"'.join(',', $drops).'"');
                    }
                }

                is_array($ceilVal) && $ceilVal = implode("\n", $ceilVal);
                $objPHPExcel->getActiveSheet()->setCellValue($ceilName, "\t".$ceilVal);
                //$objPHPExcel->getActiveSheet()->getStyle($ceilName)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $ceilIndex++;
            }
            $index++;
        }

        ob_end_clean();
        header('Set-Cookie: fileDownload=true; path=/'); //添加此cookie便于jquery.fileDownload.js判断下载状态
        header("Content-type: text/html; charset=utf-8");
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');

        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if ($ext == "xls") {
            $filename = $filename.'x';
        }

        $encoded_filename = urlencode($filename);
        $ua = $_SERVER["HTTP_USER_AGENT"];
        if (preg_match("/MSIE/", $ua)) {
            Header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
        } elseif (preg_match("/Firefox/", $ua)) {
            Header('Content-Disposition: attachment; filename*="utf8\'\'' . $filename . '"');
        } else {
            Header("Content-Disposition: attachment; filename=" . $filename);
        }

        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');

        exit();
    }

    public static function curlGet($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPGET, 1);
        $result = curl_exec($ch);
        if( curl_errno($ch)!=0 ) {
            \Yii::error( "url:$url msg:".curl_error($ch) );
        }
        curl_close($ch);
        return $result;
    }

}
