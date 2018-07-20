<?php
 namespace app\modules\repast\services;
use app\modules\ServiceBase;
use app\framework\utils\PagingHelper;
use app\modules\repast\repositories\RepastRepository;
class RepastService  extends ServiceBase{
    private $_repastRepository;
    public function __construct(RepastRepository $repastRepository)
    {
        $this->_repastRepository = $repastRepository;
        
    }

    public function getData($lng=0,$lat=0,$typePid='',$pageIndex=1,$pageSize=10,$city_id,$type_id,$keyword){
        $rs = $this->_repastRepository->getData($lng,$lat,$typePid,$pageIndex,$pageSize,$city_id,$type_id,$keyword);
        foreach ($rs as $v) {
            $v['dis'] = round($v['dis'],2);
        }
        return $rs;
    }

    
}
