<?php
namespace app\modules\home\services;
use app\modules\home\repositories\AllRepository;

use app\modules\ServiceBase;
class AllService extends ServiceBase {

    private $_allRepository;
    
    public function __construct(AllRepository $allRepository)
    {
        $this->_allRepository = $allRepository;
    }



    public function getTypeInfo()
    {
        return $this->_allRepository->getTypeInfo();
    }


}
