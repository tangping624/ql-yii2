<?php

namespace app\framework\weixin\msgtemplate;

    /*
     * To change this license header, choose License Headers in Project Properties.
     * To change this template file, choose Tools | Templates
     * and open the template in the editor.
     */

/**
 * 面积补差提醒消息模板
 *
 * @author kongy
 */
class AreaBalanceRemind implements IMsgTemplate
{
    const TEMPLATE_NO = 'OPENTM207463837';
    /**
     * 模板短编号
     * @var string
     */
    public $shortId = self::TEMPLATE_NO;

    /**
     * 详情url
     * @var string
     */
    public $url = '';

    /**
     * 顶部颜色  '#FF0000'
     * @var string
     */
    public $topColor = '#FF0000';

    /**
     * 前言
     * @var string
     */
    public $first;

    /**
     * 房号
     * @var string
     */
    public $room_name;

    /**
     * 客户姓名
     * @var string
     */
    public $customer_name;

    /**
     * 收费金额
     * @var string
     */
    public $pay_amount;

    /**
     * 费用说明
     * @var string
     */
    public $fee_desc;

    /*
     * 备注
     */
    public $remark;

    public function __construct($first, $pay_amount, $fee_desc, $customer_name, $room_name, $remark, $url)
    {
        $this->url = $url;
        $this->first = $first;
        $this->pay_amount = $pay_amount;
        $this->fee_desc = $fee_desc;
        $this->customer_name = $customer_name;
        $this->room_name = $room_name;
        $this->remark = $remark;
    }

    public function getData()
    {
        $data = [
            'first' => ['value' => $this->first, 'color' => '#000000'],
            'keyword1' => ['value' => $this->pay_amount, 'color' => '#000000'],
            'keyword2' => ['value' => $this->fee_desc, 'color' => '#000000'],
            'keyword3' => ['value' => $this->customer_name, 'color' => '#000000'],
            'keyword4' => ['value' => $this->room_name, 'color' => '#000000'],
            'remark' => ['value' => $this->remark, 'color' => '#000000'],
        ];

        return $data;
    }
}