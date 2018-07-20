<?php
namespace app\modules\repast\controllers;
use app\controllers\ControllerBase;
use app\modules\repast\services\RepastService;
use app\modules\pub\models\ListForm;
use app\modules\wiki\services\WikiService;
use app\modules\pub\services\PublicService;
class RepastController extends ControllerBase{
    private $_repastService;
    private $_wikiService;
    private $_publicService;
    public function __construct($id, $module,RepastService $repastService,WikiService $wikiService ,PublicService $publicService, $config = [])
    {
        $this->_repastService = $repastService;
        $this->_wikiService = $wikiService;
        $this->_publicService = $publicService;
        parent::__construct($id, $module, $config);
    }


    public function actionIndex($id='',$appcode='')
    {

        $advert=$this->_wikiService->getAdvert($appcode);//获取移民广告
        $type= $this->_publicService->getType($id);//获取百科分类
        $news= $this->_publicService->getNews($id);//获取百科新鲜事
        $city= $this->_publicService->getCity();//获取区域城市

        return  $this->render('index',['advert'=>$advert,'type'=>$type,'news'=>$news,'city'=>$city]);
    }

    //url: /ctrip/ctrip/ajax-get-seller-list
    public function actionAjaxGetSellerList($lng=0,$lat=0,$typePid='',$pageIndex=1,$pageSize=10,$city_id='',$type_id='',$keyword='')
    {
        $data = $this->_repastService->getData($lng, $lat, $typePid, $pageIndex, $pageSize,$city_id,$type_id,$keyword);
        return $this->json($data);
    }






}
