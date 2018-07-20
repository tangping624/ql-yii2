<?php

namespace app\modules\wechat\models;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use app\framework\web\extension\FormBase;
use app\framework\weixin\helper\Article;

/**
 * Description of NewsForm
 *
 * @author Chenxy
 */
class NewsForm extends FormBase
{
    /**
     * 类型
     */
    public $type = 'news';

    /**
     * 标题
     * @var type 
     */
    public $title;
    
    /**
     * 封面url
     * @var type 
     */
    public $cover_img_url;
    
    /**
     * 是否显示封面
     * @var type 
     */
    public $is_body_cover;
    
    /**
     * 内容
     * @var type 
     */
    public $description;
    
    /**
     * 原文链接
     * @var type 
     */
    public $original_url;
    
    /**
     * 摘要
     * @var type 
     */
    public $digest;
    
    /**
     * 作者
     * @var type 
     */
    public $author;


    /**
     * 校验规则
     * @return array
     */
    public function rules()
    {
        // 标题、图文、正文必填
        return [
            [['title', 'cover_img_url', 'description'], 'required', 'message' => '缺少参数'],
             ['title', 'string', 'max' => 64, 'tooLong' => '标题超过字数限制'],
             ['digest', 'string', 'max' => 120, 'tooLong' => '摘要超过字数限制'],
             ['description', 'string', 'max' => 20000, 'tooLong' => '正文超过字数限制']
        ];
    }

    public function ConvertToActicel()
    {
        $acticel = new Article();
        $acticel->title = $this->title;
        $acticel->author = $this->author;
        $acticel->content = $this->description;
        $acticel->content_source_url = $this->original_url;
        $acticel->digest = $this->digest;
        $acticel->show_cover_pic = !empty($this->is_body_cover);
        return $acticel;
    }
}
