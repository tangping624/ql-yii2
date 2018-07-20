<?php
 namespace app\modules\tour\services;
use app\modules\ServiceBase;
use app\framework\utils\PagingHelper;
use app\modules\tour\repositories\TourRepository;
class TourService  extends ServiceBase{
    private $_tourRepository;
    public function __construct(TourRepository $tourRepository)
    {
        $this->_tourRepository = $tourRepository;
        
    }

   public function getTourList($pagesize=10 , $page =1,$type_pid,$type_id,$city_id,$keyword,$city_pid){
     if ($page < 0) {
      throw new \InvalidArgumentException('$page');
     }
      if ($pagesize <= 0) {
      throw new \InvalidArgumentException('$pagesize');
      }
       $skip = PagingHelper::getSkip($page, $pagesize);
      return  $this->_tourRepository->getTourList($skip,$pagesize,$type_pid,$type_id,$city_id,$keyword,$city_pid);
 }

    
}
