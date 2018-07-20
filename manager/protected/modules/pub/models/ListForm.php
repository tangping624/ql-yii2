<?php 
namespace app\modules\pub\models;
use app\framework\web\extension\FormBase;
/**
 * 列表
 */
class ListForm extends FormBase {

    /**
     * @var 列表
     */
    public $items;

    /**
     * @var 总记录数
     */
    public $total;

    /**
     * @var 当前页
     */
    public $page;

    /**
     * @var pageSize
     */
    public $pageSize;

}