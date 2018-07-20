<?php
namespace app\modules\wechat\services;

/**
 * @todo 粉丝管理
 * @author fanwq
 */
use app\modules\ServiceBase;
use app\modules\wechat\repositories\FanRepository;

class FanService extends ServiceBase
{
    private $_fanRepository;

    public function __construct(FanRepository $fanRepository)
    {
        $this->_fanRepository = $fanRepository;
    }

    /**
     *统计记录
     */
    public function getFansCount($param)
    {
        return $this->_fanRepository->getFansCount($param);
    }

    /**
     * 获取全部用户
     * @param type $param
     * @return type
     */
    public function initAllList($param)
    {
        $result = $this->getFansCount($param);
        $result['total'] = $result['all'];
        if ($result['total'] == 0) {
            $result['items'] = [];
            return $result;
        }
        $param['condition'] = " 1=1 ";
        $result['items'] = $this->_fanRepository->getFansList($param);
        return $result;
    }

    /**
     * 获取粉丝列表
     * @param type $param
     * @return type
     */
    public function fansList($param)
    {
        $result = $this->getFansCount($param);
        $result['total'] = $result['fan'];
        $result['items'] = [];
        if ($result['fan'] == 0) {
            return $result;
        }
        $param['condition'] = " (f.member_id is null or f.member_id='') ";
        $result['items'] = $this->_fanRepository->getFansList($param, 'fans');
        return $result;
    }

    /**
     * 获取会员列表
     * @param type $param
     * @return type
     */
    public function memberList($param)
    {
        $result = $this->getFansCount($param);
        $result['total'] = $result['member'];
        $result['items'] = [];
        if ($result['member'] == 0) {
            return $result;
        }
        $param['condition'] = " f.member_id is not null and f.member_id!='' ";
        $result['items'] = $this->_fanRepository->getFansList($param, 'member');
        return $result;
    } 

}
