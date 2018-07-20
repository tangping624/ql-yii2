<?php
 namespace app\modules\city\services;
 
use app\modules\ServiceBase;
use app\framework\utils\PagingHelper;
use app\modules\city\repositories\OfficersRepository;
class OfficersService  extends ServiceBase{
    private $_officersRepository;
    public function __construct(OfficersRepository $officersRepository)
    {
        $this->_officersRepository = $officersRepository; 
    }

    public function getEntity($id){
        if(empty($id)){
            throw new \InvalidArgumentException('$id');
        }
        return $this->_officersRepository->getMOfficersEntity($id);

    }

    public function findMaxCode(){
        return $this->_officersRepository->findMaxCode();
    }
    public function saveInfo($MOfficers){
        $rst = $this->_officersRepository->saveMOfficersInfo($MOfficers);
        return $rst;
    }

    public function getMOfficersInfo($id){
        if(empty($id)){
            throw new \InvalidArgumentException('$id');
        }
        $rst = $this->_officersRepository->getMOfficersInfo($id);
        return $rst;
    }
    public function deleteMOfficersInfo($id){
        if(empty($id)){
            throw new \InvalidArgumentException('$id');
        }
        $rst = $this->_officersRepository->deleteMOfficersInfo($id);
        return $rst;
    }

    public function getshowList( ){

        $rst = $this->_officersRepository->getshowList( );
        return $rst;
    }

    public function setDefaultCity($id){
        if(empty($id)){
            throw new \InvalidArgumentException('$id');
        }
        $rst = $this->_officersRepository->setDefaultCity($id);
        return $rst;
    }

}
