<?php 
namespace app\modules\home\controllers;
use app\controllers\ControllerBase;
use app\modules\home\services\CityService;
class CityController extends ControllerBase
{
    private $_cityService;

    public function __construct($id, $module, CityService $cityService, $config = [])
    {
        $this->_cityService = $cityService;
        parent::__construct($id, $module, $config);
    }

    //首页
    public function actionIndex()
    {
        $citys = $this->_cityService->getCity();
        return $this->render('index', ['citys' => $citys]);
    }

    //ajax-get-city
    public function actionAjaxGetCity($name='')
    {
        $citys = $this->_cityService->getCityByName($name);
        return $this->json($citys);
    }

    //ajax-loc-city
    public function actionAjaxLocCity($name='')
    {
        $citys = $this->_cityService->locCityByName($name);
        return $this->json($citys);
    }
}
