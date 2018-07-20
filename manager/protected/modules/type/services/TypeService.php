<?php
namespace app\modules\type\services;

use app\modules\ServiceBase;
use app\framework\utils\PagingHelper;
use app\modules\type\repositories\TypeRepository;
use app\modules\merchant\repositories\SMerchantRepository;
class TypeService  extends ServiceBase{
    private $_typeRepository;
    private $_sMerchantRepository;
    public function __construct(TypeRepository $typeRepository,SMerchantRepository $sMerchantRepository)
    {
        $this->_typeRepository = $typeRepository;
        $this->_sMerchantRepository = $sMerchantRepository;
    }

    public function getTypeList(){

        return  $this->_typeRepository->getTypeList();

    }

    public function saveType($stype){
        if (empty($stype)) {
            throw new \InvalidArgumentException('$stype');
        }
        return  $this->_typeRepository->saveType($stype);
    }

    public function deleteType($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return  $this->_typeRepository->deleteType($id);
    }

    public function getTypeSeller($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
       return   $this->_sMerchantRepository->getTypeSeller($id);
    }

    public function findSellerType($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return  $this->_typeRepository->findSellerType($id);

    }

    public function getParentType(){

        return $this->_typeRepository->getParentType();
    }

    public function findSellerSon($id){
        return $this->_typeRepository->findSellerSon($id);

    }

    public function findMaxCode(){
        return $this->_typeRepository->findMaxCode();

    }

    public function setDisplay($id,$is_display){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return  $this->_typeRepository->setDisplay($id,$is_display);
    }

}
