<?php
namespace app\modules\baike\services;
use app\modules\ServiceBase;
use app\modules\baike\repositories\BaikeRepository;
use app\modules\baike\repositories\ManageRepository;
use app\framework\utils\PagingHelper;
class BaiKeService  extends ServiceBase{
    private $_baikeRepository;
    private $_manageRepository;
    public function __construct(BaikeRepository $baikeRepository,ManageRepository $manageRepository)
    {
        $this->_baikeRepository = $baikeRepository;
        $this->_manageRepository = $manageRepository;
        
    }

    public function getBaikeTYpe(){

   return $this->_baikeRepository->getBaikeTYpe();

  }

    public function getEnitity($id){
      if (empty($id)) {
      throw new \InvalidArgumentException('$id');
     }
     return $this->_baikeRepository->getEnitity($id);
 }

    public function saveType( $category){
     if (empty( $category)) {
      throw new \InvalidArgumentException(' $category');
     }
     return $this->_baikeRepository->saveType( $category);
    }

   public function deleteType($id){
     if (empty($id)) {
   throw new \InvalidArgumentException('$id');
    }
     return  $this->_baikeRepository->deleteType($id);
  }

    public function getTypeWiki($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return   $this->_manageRepository->getTypeWiki($id);

    }

    
    
}
