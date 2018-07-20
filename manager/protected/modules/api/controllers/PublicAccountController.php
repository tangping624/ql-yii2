<?php

namespace app\modules\api\controllers;

use app\controllers\WxapiControllerBase;
use app\modules\api\services\PublicAccountService;

class PublicAccountController extends WxapiControllerBase
{
    /**
     * @var PublicAccountService
     */
    private $_publicAccountService;

    public function __construct($id, $module, PublicAccountService $publicAccountService, $config = [])
    {
        $this->_publicAccountService = $publicAccountService;
        parent::__construct($id, $module, $config);
    }

    public function actionGetMch()
    {
        if (!isset($_GET['account_id'])) {
            return $this->json(['errcode' => 1002, 'errmsg' => '缺少account_id参数!']);
        }

        $accountId = $_GET['account_id'];
        if (empty($accountId)) {
            return $this->json(['errcode' => 1002, 'errmsg' => 'account_id参数为空!']);
        }
        try {
            $mch = $this->_publicAccountService->getMch($accountId);
            if($mch == false){
                return $this->json(['errcode' => 1002, 'errmsg' => '对应accountId:' . $accountId . ', 记录不存在!']);
            }

            $result = ['mch_half_key' => $mch['mch_half_key']];
            return $this->json($result);
        } catch (\Exception $ex) {
            return $this->json(['errcode' => 40013, 'errmsg' => $ex->getMessage()]);
        }
    }

    //获取公众号id和微信的原始id
    public function actionGetAccountId()
    {
        if(!isset($_GET['corp_id'])){
            return $this->json(['errcode' => 1002, 'errmsg' => '缺少corp_id参数']);
        }

        $corpId = $_GET['corp_id'];
        if(empty($corpId)){
            return $this->json(['errcode' => 1002, 'errmsg' => 'corp_id参数不能为空']);
        }

        $result = $this->_publicAccountService->getAccountIdByCorpId($corpId);
        if($result == false){
            return $this->json(['errcode' => 40013, 'errmsg' => '找不到account']);
        }else {
            return $this->json(['account_id' => $result['id'], 'original_id' => $result['original_id']]);
        }

    }

}