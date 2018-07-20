<?php
 namespace app\modules\appapi\services;
use app\modules\ServiceBase;
use app\framework\utils\PagingHelper;
use app\modules\appapi\repositories\LobbyRepository;
class LobbyService  extends ServiceBase{
    private $_lobbyRepository;
    public function __construct(LobbyRepository $lobbyRepository)
    {
        $this->_lobbyRepository = $lobbyRepository;
        
    }


 public function getBlogList($pagesize=10 , $page =1){
    if ($page < 0) {
     throw new \InvalidArgumentException('$page');
    }
    if ($pagesize <= 0) {
      throw new \InvalidArgumentException('$pagesize');
   }
    $skip = PagingHelper::getSkip($page, $pagesize);
     return  $this->_lobbyRepository->getBlogList($skip,$pagesize);
 }

  public function updateQuantity($id){
   if (empty($id)) {
    throw new \InvalidArgumentException('$id');
   }
  return  $this->_lobbyRepository->updateQuantity($id);
}
   public function getDetails($id, $memberId){
    if (empty($id)) {
     throw new \InvalidArgumentException('$id');
    }
    return $this->_lobbyRepository->getDetails($id, $memberId);
   }

    public function clickPraise($id,$memberId,$type){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return $this->_lobbyRepository->clickPraise($id,$memberId,$type);
    }
}
