<?php
namespace app\modules\map\controllers;
use app\controllers\ControllerBase;
use app\modules\pub\models\ListForm;
use app\modules\news\services\NewsService;
use app\framework\utils\StringHelper;
use app\modules\news\models\MNewsForm;
class MapController  extends ControllerBase{
    private $_newsService;
    public function __construct($id, $module,NewsService $newsService, $config = [])
    {
        $this->_newsService = $newsService;
        parent::__construct($id, $module, $config);
    }


    //地图
    public function actionMap()
    {
        return $this->renderPartial('map');
    }

}
