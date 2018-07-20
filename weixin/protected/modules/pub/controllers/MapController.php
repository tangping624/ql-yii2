<?php
namespace app\modules\pub\controllers;
use app\controllers\ControllerBase;

class MapController extends ControllerBase{


    //地图
    public function actionMap()
    {
        return $this->renderPartial('map');
    }

}
