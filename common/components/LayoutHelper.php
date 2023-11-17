<?php

namespace common\components;
;

/**
 * Class LayoutHelper
 * @package common\widgets
 */
class LayoutHelper
{

    /**
     * @param $header
     * @param $color
     * @param $width
     * @param $id
     * @return string
     * @desc
     *  primary 深蓝
     *  info 浅蓝
     *  danger 红色
     *  success 绿色
     */
    public static function boxBegin($header = false, $color = 'default', $width = 12, $id = '')
    {
        $headHtml = '';
        $idHtml = '';
        if ($header !== false) {
            $headHtml = '<div class="box-header with-border"><h3 class="box-title">' . $header . '</h3><div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button></div></div>';
        }
        if (!empty($id)) {
            $idHtml = 'id = ' . $id;
        }
        $html = '<div class="col-md-' . $width . '"><div class="box box-' . $color . '">' . $headHtml . '<div class="box-body" ' . $idHtml . '>';
        return $html;
    }

    public static function boxEnd()
    {
        return '</div></div></div>';
    }
    /**
     * @param $header
     * @param $color
     * @param $width
     * @return string
     * @desc
     *  primary 深蓝
     *  info 浅蓝
     *  danger 红色
     *  success 绿色
     */
    public static function boxBeginButton($header = false, $color = 'default', $width = 12)
    {
        $headHtml = '';
        if ($header !== false) {
            $headHtml = '<div class="box-header with-border"><h3 class="box-title">' . $header . '</h3><div class="box-tools pull-right">
            <button style="float:right;display:none;margin-left:10px;" type="button" class="btn btn-primary btn-sm" id="cancel-info">取消</button><button style="float:right;display:none;" type="button" class="btn btn-primary btn-sm" id="save-info">保存</button><button style="float:right;" type="button" class="btn btn-primary btn-sm" id="update-info">修改</button></div></div>';
        }
        $html = '<div class="col-md-' . $width . '"><div class="box box-' . $color . '">' . $headHtml . '<div class="box-body">';
        return $html;
    }

}