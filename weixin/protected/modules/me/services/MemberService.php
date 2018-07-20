<?php
namespace app\modules\me\services;
use app\modules\me\repositories\MemberRepository;
use app\modules\me\repositories\CollectionRepository;
use app\modules\lobby\repositories\LobbyRepository;

use app\framework\utils\PagingHelper;
use app\modules\ServiceBase;
class MemberService extends ServiceBase{

    private $_collectionRepository;
    private $_lobbyRepository;

    public function __construct(CollectionRepository $collectionRepository, LobbyRepository $lobbyRepository)
    {
        $this->_collectionRepository=$collectionRepository;
        $this->_lobbyRepository=$lobbyRepository;


    }

    public function getTrack( $pagesize,$page, $memberId,$type){
        if ($page < 0) {
            throw new \InvalidArgumentException('$page');
        }
        if ($pagesize <= 0) {
            throw new \InvalidArgumentException('$pagesize');
        }
        $skip = PagingHelper::getSkip($page, $pagesize);

        return  $this->_collectionRepository->getTrack( $skip,$pagesize,$memberId,$type);

    }

    public function getCollection( $pagesize,$page, $memberId,$type){
        if ($page < 0) {
            throw new \InvalidArgumentException('$page');
        }
        if ($pagesize <= 0) {
            throw new \InvalidArgumentException('$pagesize');
        }
        $skip = PagingHelper::getSkip($page, $pagesize);

        return  $this->_collectionRepository->getCollection( $skip,$pagesize,$memberId,$type);

    }

    public function getPraise( $pagesize,$page, $memberId,$type){
        if ($page < 0) {
            throw new \InvalidArgumentException('$page');
        }
        if ($pagesize <= 0) {
            throw new \InvalidArgumentException('$pagesize');
        }
        $skip = PagingHelper::getSkip($page, $pagesize);

        return  $this->_collectionRepository->getPraise( $skip,$pagesize,$memberId,$type);

    }

    public function getLobbyList($pagesize,$page, $memberId){
        if ($page < 0) {
            throw new \InvalidArgumentException('$page');
        }
        if ($pagesize <= 0) {
            throw new \InvalidArgumentException('$pagesize');
        }
        $skip = PagingHelper::getSkip($page, $pagesize);
        return  $this->_lobbyRepository->getLobbyList($skip,$pagesize,$memberId);

    }
    public  function saveBlog($blog, $memberId){
        if (empty($blog)) {
            throw new \InvalidArgumentException('$blog');
        }
        $blog->source=2;
        $blog->created_on=date('Y-m-d H:i:s');
        $blog->created_by= $memberId;
        $blog->modified_on=date('Y-m-d H:i:s');
        $blog->modified_by= $memberId;
        $blog->ll_sum=0;
        $blog->dz_num=0;
        $blog->sc_num=0;
        return  $this->_lobbyRepository->saveBlog($blog);

    }


    public function deleteTrack($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return  $this->_collectionRepository->deleteTrack($id);
    }

    public function getLobby($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }

        return  $this->_lobbyRepository->getLobby($id);
    }

    public function deleteLobby($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return  $this->_lobbyRepository->deleteLobby($id);
    }

}
