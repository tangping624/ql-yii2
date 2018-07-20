<?php
 namespace app\modules\advertise\services;
use app\modules\advertise\repositories\AdvertRepository;
use app\modules\advertise\repositories\AdsenseRepository;
use app\modules\advertise\repositories\ImageRepository;
use app\modules\ServiceBase;
use app\entities\advert\AAdvert;
use app\framework\utils\PagingHelper;
use app\framework\utils\DateTimeHelper;
use app\framework\utils\CheckResult;
class AdvertService extends ServiceBase{
  //析构
    private $_advertRepository;
    private $_adsenseRepository;
    private $_imageRepository;
    public function __construct(AdvertRepository $advertRepository,AdsenseRepository $adsenseRepository,ImageRepository $imageRepository)
    {
        $this->_advertRepository = $advertRepository;
        $this->_adsenseRepository =$adsenseRepository;
        $this->_imageRepository = $imageRepository;
    }
 
    //广告管理列表
    public function getAdvertList($pagesize=10 , $page =1){
        if ($page < 0) {
            throw new \InvalidArgumentException('$page');
        }
        if ($pagesize <= 0) {
            throw new \InvalidArgumentException('$pagesize');
        }
        $skip = PagingHelper::getSkip($page, $pagesize);
        return  $this->_advertRepository->getAdvertList($skip,$pagesize);
    }

    //编辑
    public function getAdvertDetails($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        $advert = $this->_advertRepository->getAdvert($id);
        $images= $this->_imageRepository->getImageByFid($id);
        return ['advert'=>$advert,'images'=>$images];
    }
    
    public function getAdsense(){
       return $this->_adsenseRepository->getAdsenses();
      }

    public function getAdvert($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return $this->_advertRepository->getAdvert( $id);
    }
    
    
    //删除
    public function setDeleted($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return $this->_advertRepository->setDeleted($id);
    }
    
    //保存
    public function saveAdvert(AAdvert $advert, $isNew,$images,$user_id){
        $check=new  CheckResult();
        if(!empty($advert->adsenseid)){
            if($isNew){
                $result =  $this->_advertRepository->checkAdsense($advert->adsenseid) ;
            }else{
                $result =  $this->_advertRepository->checkAdsense($advert->adsenseid,$advert->id) ;
            }
            if($result){
                $check->setIsSuccess(false);
                $check->setMsg('广告位在已有广告！');
                return $check;
            }
        }
        $advert->created_by = $user_id;
        $advert->modified_by = $user_id;
        $advert->created_on = date('Y-m-d H:i:s', time());
        $advert->modified_on = date('Y-m-d H:i:s', time());
        if(!$isNew){
            $this->_imageRepository->deleteByFid($advert->id);
        }
        $this->_advertRepository->save($advert);
        $this->_imageRepository->add($images);
        return $check;
    }


}
