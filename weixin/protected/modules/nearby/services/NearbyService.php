<?php
namespace app\modules\nearby\services;

use app\modules\nearby\repositories\NearRepository;
use app\modules\ServiceBase;

class NearbyService extends ServiceBase
{

    private $_nearbyRepository;

    public function __construct(NearRepository $nearRepository)
    {
        $this->_nearbyRepository = $nearRepository;
    }

    public function getData($lng=0,$lat=0,$typePid='',$pageIndex=1,$pageSize=10){
        $rs = $this->_nearbyRepository->getData($lng,$lat,$typePid,$pageIndex,$pageSize);
        foreach ($rs as $v) {
            $v['dis'] = round($v['dis'],2);
        }
        return $rs;
    }
}
