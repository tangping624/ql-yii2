<?php
 namespace app\modules\member\services;
use app\modules\ServiceBase;
use app\framework\utils\PagingHelper;
use app\modules\member\repositories\MemberRepository;
class MemberService  extends ServiceBase{
    private $_memberRepository;
    public function __construct(MemberRepository $memberRepository)
    {
        $this->_memberRepository = $memberRepository;
        
    }

    //会员管理列表
    public function getMemberList($page,$pagesize,$Keywords)
    {
        if ($page < 0) {
            throw new \InvalidArgumentException('$page');
        }
        if ($pagesize <= 0) {
            throw new \InvalidArgumentException('$pagesize');
        }
        
        $skip = PagingHelper::getSkip($page, $pagesize);
        return  $this->_memberRepository->getMemberList($skip, $pagesize,$Keywords);
    }
    
}
