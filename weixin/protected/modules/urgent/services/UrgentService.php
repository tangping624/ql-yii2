<?php
namespace app\modules\urgent\services;
use app\modules\urgent\repositories\UrgentRepository;
use app\modules\ServiceBase;
use app\framework\utils\PagingHelper;
class UrgentService  extends ServiceBase
{
    private $_urgentRepository;

    public function __construct(UrgentRepository $urgentRepository)
    {
        $this->_urgentRepository=$urgentRepository;

    }


    public function getEmergencyList($pagesize=10 , $page =1){
        if ($page < 0) {
            throw new \InvalidArgumentException('$page');
        }
        if ($pagesize <= 0) {
            throw new \InvalidArgumentException('$pagesize');
        }
        $skip = PagingHelper::getSkip($page, $pagesize);
        return  $this->_urgentRepository->getEmergencyList($skip,$pagesize);
    }

    public function getDetails($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return $this->_urgentRepository->getDetails($id);
    }


}