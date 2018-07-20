<?php
 namespace app\modules\lobby\services;
use app\modules\ServiceBase;
use app\framework\utils\PagingHelper;
use app\modules\lobby\repositories\LobbyRepository;
class LobbyService  extends ServiceBase{
    private $_lobbyRepository;
    public function __construct(LobbyRepository $lobbyRepository)
    {
        $this->_lobbyRepository = $lobbyRepository;
        
    }

 //百科管理列表
   public function getBlogList($pagesize=10 , $page =1,$keywords){
     if ($page < 0) {
      throw new \InvalidArgumentException('$page');
     }
    if ($pagesize <= 0) {
      throw new \InvalidArgumentException('$pagesize');
    }
     $skip = PagingHelper::getSkip($page, $pagesize);
      return  $this->_lobbyRepository->getBlogList($skip,$pagesize,$keywords);
 }


     public function saveBlog( $blog,$user_id, $isNew){

        if(!$isNew){
            $blog->modified_on = date('Y-m-d H:i:s', time());
        }else{
            $blog->created_by = $user_id;
            $blog->ll_sum =0;
            $blog->modified_by = $user_id;
            $blog->created_on = date('Y-m-d H:i:s', time());
            $blog->modified_on = date('Y-m-d H:i:s', time());
        }
         $blog->source = 1;
         $blog->member_id = $user_id;
        $re= $this->_lobbyRepository->save($blog);

        return $re;
    }

     public function getWiki($id){
      if (empty($id)) {
         throw new \InvalidArgumentException('$id');
        }
        return  $this->_lobbyRepository->getWiki($id);

 }


       public function deleteWikiInfo($id){
        if (empty($id)) {
       throw new \InvalidArgumentException('$id');
     }
        return  $this->_lobbyRepository->deleteWikiInfo($id);
    }




}
