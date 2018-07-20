<?php
namespace app\modules\baike\services;
use app\modules\ServiceBase;
use app\modules\baike\repositories\EmergencyRepository;
use app\framework\utils\PagingHelper;
class EmergencyService  extends ServiceBase{
    private $_emergencyRepository;
    public function __construct(EmergencyRepository $emergencyRepository)
    {
        $this->_emergencyRepository = $emergencyRepository;

    }

    //百科管理列表
    public function getBaikeList($pagesize=10 , $page =1,$keywords){
        if ($page < 0) {
            throw new \InvalidArgumentException('$page');
        }
        if ($pagesize <= 0) {
            throw new \InvalidArgumentException('$pagesize');
        }
        $skip = PagingHelper::getSkip($page, $pagesize);
        return  $this->_emergencyRepository->getBaikeList($skip,$pagesize,$keywords);
    }


    public function saveEmergency( $emergency,$user_id,$isNew){

        if(!$isNew){
              $emergency->modified_on = date('Y-m-d H:i:s', time());
        }else{
            $emergency->created_by = $user_id;
            $emergency->modified_by = $user_id;
            $emergency->created_on = date('Y-m-d H:i:s', time());
            $emergency->modified_on = date('Y-m-d H:i:s', time());
        }
       $re= $this->_emergencyRepository->save($emergency);

        return $re;
    }

    public function getWiki($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return  $this->_emergencyRepository->getWiki($id);

    }



    public function deleteWikiInfo($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return  $this->_emergencyRepository->deleteWikiInfo($id);
    }



}
