<?php
namespace app\modules\me\services;
use app\modules\me\repositories\MemberRepository;
use app\services\UploadService;
use app\framework\utils\PagingHelper;
use app\modules\ServiceBase;
class MeService extends ServiceBase{
    
    private $_memberRepository;
    protected $uploadService;

    
    public function __construct(MemberRepository $memberRepository,UploadService  $uploadService)
    {
        $this->_memberRepository=$memberRepository;
        $this->uploadService = $uploadService;

    }

    public function checkUser($mobile, $pwd){
        if (empty($mobile)) {
            throw new \InvalidArgumentException('$mobile');
        }
        if (empty($pwd)) {
            throw new \InvalidArgumentException('$pwd');
        }
       return  $this->_memberRepository->checkUser($mobile, $pwd);
    }

    public function changePwd($mobile, $pwd){
        if (empty($mobile)) {
            throw new \InvalidArgumentException('$mobile');
        }
        if (empty($pwd)) {
            throw new \InvalidArgumentException('$pwd');
        }
        return  $this->_memberRepository->changePwd($mobile, $pwd);

    }

    public function SaveUser($mobile, $pwd){
        if (empty($mobile)) {
            throw new \InvalidArgumentException('$mobile');
        }
        if (empty($pwd)) {
            throw new \InvalidArgumentException('$pwd');
        }
        return  $this->_memberRepository->SaveUser($mobile, $pwd);
    }

    public function findUser($mobile){
        if (empty($mobile)) {
            throw new \InvalidArgumentException('$mobile');
        }
        return  $this->_memberRepository->findUser($mobile);
    }

    public function getUser( $memberId){

        if (empty($memberId)) {
            throw new \InvalidArgumentException('$memberId');
        }
        return  $this->_memberRepository->getUser($memberId);
    }

    public function updatePhoto($id,$headimg_url){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }

        return  $this->_memberRepository->updatePhoto($id, $headimg_url);
    }

    public function updateName($id,$name){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return  $this->_memberRepository->updateName($id,$name);
    }

    public function updatePwd($id, $npwd){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return  $this->_memberRepository->updatePwd($id, $npwd);
    }



   
}
