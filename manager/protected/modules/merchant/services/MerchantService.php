<?php
namespace app\modules\merchant\services;
use app\modules\ServiceBase;
use app\modules\merchant\repositories\SImagesRepository;
use app\modules\merchant\repositories\SellerToTagRepository;
use app\modules\merchant\repositories\SellerTypeRepository;
use app\modules\city\repositories\OfficersRepository;
use app\framework\utils\DateTimeHelper;
use app\modules\merchant\repositories\SMerchantRepository;
use app\framework\utils\PagingHelper;
class MerchantService extends ServiceBase{
    
    private $_sImagesRepository;
    private $_sellerToTagRepository;
    private $_sellerTypeRepository;
    private $_sMerchantRepository;
    private $_officersRepository;

    public function __construct(SImagesRepository $sImagesRepository,SellerToTagRepository $sellerToTagRepository,SellerTypeRepository $sellerTypeRepository,SMerchantRepository $sMerchantRepository,OfficersRepository $officersRepository)
    {
        $this->_sImagesRepository = $sImagesRepository;
        $this->_sellerToTagRepository = $sellerToTagRepository;
        $this->_sellerTypeRepository = $sellerTypeRepository;
        $this->_sMerchantRepository = $sMerchantRepository;
        $this->_officersRepository = $officersRepository;
    }

    //商家列表
    public function getSellerList($pagesize=10 , $page =1,$name){
        if ($page < 0) {
            throw new \InvalidArgumentException('$page');
        }
        if ($pagesize <= 0) {
            throw new \InvalidArgumentException('$pagesize');
        }
        $skip = PagingHelper::getSkip($page, $pagesize);
        return  $this->_sMerchantRepository->getSellerList($skip,$pagesize,$name);
    }

    //删除
    public function setDeleted($id,$userid){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        if (empty($userid)) {
            throw new \InvalidArgumentException('$userid');
        }
        $time = DateTimeHelper::now();
        return $this->_sMerchantRepository->setDeleted($id, $time,$userid);
    }

    public function getMerchantInfo($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return  $this->_sMerchantRepository->getMerchantInfo($id);
    }

    public function getImageInfo($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return  $this->_sImagesRepository->getImageInfo($id);
    }

    public function getmerchantType($pid,$id){

        $data1= $this->_sellerTypeRepository->getmerchantPType($pid);
        $data2= $this->_sellerTypeRepository->getmerchantType($id);
        return ['pname'=>$data1,'name'=>$data2];
}

    public function saveGoods( $goods, $userId, $isNew,$image){
        if($isNew){
            $goods->dz_num=0;
            $goods->ll_num=0;
            $goods->sc_num=0;
            $goods->created_by=$userId;
            $goods->created_on = DateTimeHelper::now();
            $goods->modified_by=$userId;
            $goods->modified_on = DateTimeHelper::now();
            $goods->is_deleted=0;
        }else{
            $goods->modified_by=$userId;
            $goods->modified_on = DateTimeHelper::now();
        }
        if(!$isNew){
            $this->_sImagesRepository->deleteByFid($goods->id);
            //$this->_sellerToTagRepository->deleteByFid($goods->id);

        }
        $res= $this->_sImagesRepository->add($image);
        $res= $this->_sMerchantRepository->save($goods);

        return $res;
    }

    public function changeGoods($id,$is_recommend,$userId){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        if (empty($userId)) {
            throw new \InvalidArgumentException('$userId');
        }
        $time = DateTimeHelper::now();
        $data=$this->_sMerchantRepository->changeGoodsIsShelves($id,$is_recommend,$userId,$time);
        return $data;
    }

    public function findCity()
    {
        return $this->_officersRepository->findCity();
    }

    public function getTag(){
        return $this->_sellerToTagRepository->getTag();
    }
    public function findSon($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return $this->_officersRepository->findSon($id);
    }

    public function saveSellerTag($seller_id, $tag_id){
        if (empty($seller_id)) {
            throw new \InvalidArgumentException('$seller_id');
        }
        if (empty($tag_id)) {
            throw new \InvalidArgumentException('$tag_id');
        }
        return  $this->_sellerToTagRepository->saveSellerTag($seller_id, $tag_id);
}
    public function  findTagInfo($id){

        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return $this->_sellerToTagRepository->findTagInfo($id);
    }
}
