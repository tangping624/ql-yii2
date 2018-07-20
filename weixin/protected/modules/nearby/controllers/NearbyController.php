<?php
namespace app\modules\nearby\controllers;
use app\controllers\ControllerBase;
use app\modules\nearby\services\NearbyService;


class NearbyController extends ControllerBase {
    private $_nearbyService;
    public function __construct($id, $module, NearbyService $nearbyService, $config = [])
    {
        $this->_nearbyService = $nearbyService;
        parent::__construct($id, $module, $config);
    }


    public function actionIndex()
    {
        $public_id = $this->context->publicId;
        $url = \Yii::$app->request->absoluteUrl;
        $wxjsdk = [];//$this->_nearbyService->getJssdksign($public_id, urldecode($url));

        $memu['navigation_list'] = $this->getNavigation('home');
        $memu['menu'] = 'nearby/nearby/index';
        $memu['public_id'] = $this->context->publicId;
        return $this->render('index', ['menu' => $memu, 'wxjsdk' => $wxjsdk]);
    }

    //url: /nearby/nearby/ajax-get-seller-list
    public function actionAjaxGetSellerList($lng=0,$lat=0,$typePid='',$pageIndex=1,$pageSize=10)
    {
        $data = $this->_nearbyService->getData($lng, $lat, $typePid, $pageIndex, $pageSize);
        return $this->json($data);
    }
}
