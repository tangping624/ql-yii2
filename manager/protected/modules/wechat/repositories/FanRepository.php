<?php

namespace app\modules\wechat\repositories;

@ini_set('memory_limit', '512M');

use app\modules\RepositoryBase;
use app\entities\PFan;
use app\entities\member\HMember;   
use app\modules\basic\repositories\MemberLevelRepository;
use yii\db\Query;

class FanRepository extends RepositoryBase
{
    private static $pageSize = 20;

    public static function getAllList($param)
    {
        $param['condition'] = " 1=1";
        return self::_getByCondition($param);
    }

    public static function getFanList($param)
    {
        $param['condition'] = " f.member_id is null ";
        return self::_getByCondition($param);
    }

    public static function getMemberList($param)
    {
        $param['condition'] = " f.member_id is not null ";
        return self::_getByCondition($param);
    }
 
    private static function _getByCondition($param)
    {
        $connection = PFan::getDb();
        $query = (new \yii\db\Query())
            ->from(PFan::tableName() . " f")
            ->join('LEFT JOIN', HMember::tableName() . " h", 'h.id=f.member_id') 
            ->Where(['f.is_deleted' => 0, 'f.is_followed' => $param['is_followed'], 'f.account_id' => $param['account_id']]);
        $query = $query->select('f.nick_name, f.id, f.account_id, f.member_id, h.id_type, h.level_id, h.name,h.id_code, h.mobile, f.headimg_url, f.sex, f.follow_time, f.created_on, h.birthday');
        $query = $query->groupBy('f.openid');
 
        isset($param['name']) && $query->andWhere(['or', ['=', 'f.nick_name', $param['name']], ['=', 'h.name', $param['name']]]); 
        isset($param['id_code']) && $query->andWhere(['h.id_code' => $param['id_code']]); 
        isset($param['mobile']) && $query->andWhere(['h.mobile' => $param['mobile']]);
        //加入排序
        if (count($param['sort_list']) > 0) {
            $sortArr = [];
            foreach ($param['sort_list'] as $sort) {
                $sortArr[$sort->field] = ($sort->sort == "desc") ? SORT_DESC : SORT_ASC;
            }
            $query->orderBy($sortArr);
        } else {
            $query->orderBy(['created_on' => SORT_DESC]);
        }

        $pageSize = !isset($param['pageSize']) ? self::$pageSize : $param['pageSize'];
        if (isset($param["page"])) {
            $pageIndex = $param["page"];
            $offset = ($pageIndex - 1) * $pageSize;
            $query->offset($offset)->limit($pageSize);
        }

        $command = $query->createCommand($connection);
        $rows = $command->queryAll();

      
        $memberLevelRepository = new MemberLevelRepository();
        $memberLevelList = $memberLevelRepository->getMemberLevelList();
        foreach ($rows as $key => $value) {
            $rows[$key]['level'] = self::getMemberLevelById($memberLevelList, $value['level_id']); 
        }


        $result['items'] = $rows;
        sort($result['items']);
        self::_parseRows($result['items'], $param['type']);

        return $result;
    }

    public static function getSumData($param)
    {
        $result['member'] = $result['all'] = $result['fan'] = 0;
        $param['condition'] = " 1=1";
        $list = self::_getByCondition($param);
        if (empty($list['items'])) {
            return $result;
        }
        $result['all'] = intval($list['total']);
        foreach ($list['items'] as $arr) {
            if ($arr['member_id']) {
                $result['member']++;
            } else {
                $result['fan']++;
            }
        }

        return $result;
    }

    private static function _parseRows(&$rows, $userType)
    {
        $type_arr = ['member' => '会员', 'fan' => '粉丝'];
        foreach ($rows as $key => $val) {
            $val['birthday'] && $rows[$key]['birthday'] = floor((time() - strtotime($rows[$key]['birthday'])) / (86400 * 365));
            $rows[$key]['follow_time'] = date('Y-m-d', strtotime($rows[$key]['follow_time']));
            $rows[$key]['created_on'] = date('Y-m-d', strtotime($rows[$key]['created_on'])); 
            $rows[$key]['user_type'] = self::_getUserType($rows[$key]);
            $rows[$key]['type'] = empty($rows[$key]['type']) ? $type_arr[$rows[$key]['user_type']] : $rows[$key]['type'];
        }
    }

    private static function _getMemberRoom($member_id)
    {
        $query = (new \yii\db\Query())
            ->select('room_name')
            ->from(HMemberRoom::tableName())
            //->Where("member_id='$member_id' and is_deleted=0");
            ->where(['member_id' => $member_id, 'is_deleted' => 0]);

        $connection = HMemberRoom::getDb();
        $command = $query->createCommand($connection);

        $result = [];
        $rows = $command->queryAll();
        if (empty($rows)) {
            return $result;
        }
        foreach ($rows as $value) {
            $result[] = $value['room_name'];
        }

        return $result;
    }

    private static function _getUserType($rows)
    {
        return strlen($rows['member_id'])>0? 'member' : 'fan';
    }

    public static function getAll()
    {
        $connection = PFan::getDb();
        $query = (new \yii\db\Query())
            ->from(PFan::tableName())
            ->select('member_id, nick_name')
            ->Where("is_deleted=0");

        $command = $query->createCommand($connection);
        return $command->queryAll();
    }

    public static function getMemberLevelById($memberLevel, $id)
    {
        foreach ($memberLevel as $key => $value) {
            if ($value['id'] == $id) {
                return $value['name'];
            }
        }

        return '';
    } 

    /**
     * 统计总记录数
     * @param type $param
     * @param type $type
     * @return type
     */
    public function getFansCount($param, $type = null)
    {
        //$where = "  WHERE f.is_deleted=0 AND f.is_followed={$param['is_followed']} AND f.account_id='{$param['account_id']}' ";
        $table = PFan::tableName();
        $query = (new \yii\db\Query())->from($table . ' as f') 
            ->leftJoin('h_member as m', 'f.member_id=m.id')
            ->where(['f.is_deleted' => 0, 'f.is_followed' => $param['is_followed'], 'f.account_id' => $param['account_id']])
            ->select('COUNT(distinct(f.openid))');

        if ($param['is_followed'] == 0) {
            $query->andWhere('f.follow_time is not null');
        }

        //$param['name'] && $where .= " AND (f.nick_name='{$param['name']}' or m.name = '{$param['name']}' )";
        isset($param['name']) && $query->andWhere(['or', ['=', 'f.nick_name', $param['name']], ['=', 'm.name', $param['name']]]); 
        //$param['id_code'] && $where .= " AND m.id_code='{$param['id_code']}' ";
        isset($param['id_code']) && $query->andWhere(['m.id_code' => $param['id_code']]); 
        isset($param['mobile']) && $query->andWhere(['h.mobile' => $param['mobile']]);
        $connection = PFan::getDb();
        //$rows_all = $connection->createCommand($sql_all)->queryScalar();
        $queryAll = clone $query;
        $rows_all = $queryAll->createCommand($connection)->queryScalar();//var_dump($queryAll->createCommand($connection));
        //$rows_fan = $connection->createCommand($sql_fan)->queryScalar();
        $queryFan = clone $query;
        $rows_fan = $queryFan->andWhere(['or', 'f.member_id is null', "f.member_id=''"])->createCommand($connection)->queryScalar();
        

        return [
            'all' => $rows_all,
            'fan' => $rows_fan,
            'member' => $rows_all - $rows_fan 
        ];
    }

    /**
     * 获取列表信息
     * @param type $param
     * @param type $type
     * @return type
     */
    public function getFansList($param, $type = null)
    { 
        $query = (new \yii\db\Query())
            ->from(PFan::tableName() . " f")
            ->join('LEFT JOIN', HMember::tableName() . " h", 'f.member_id=h.id') 
            //->Where("f.is_deleted=0 and f.is_followed={$param['is_followed']}   and f.account_id='{$param['account_id']}' {$param['condition']} ")
            ->where(['f.is_deleted' => 0, 'f.is_followed' => $param['is_followed'], 'f.account_id' => $param['account_id']])
            ->andWhere($param['condition'])
            ->groupBy('f.openid')
            ->select('count(DISTINCT(f.openid)) as tmp, f.nick_name, f.id, f.account_id, f.member_id, h.id_type, h.level_id, h.name, h.id_code, h.mobile, h.first_login_time, f.headimg_url, f.sex, f.follow_time, f.created_on, h.birthday ');

        if ($param['is_followed'] == 0) {
            $query->andWhere('f.follow_time is not null');
        }

        isset($param['name']) && $query->andWhere(['or', ['=', 'f.nick_name', $param['name']], ['=', 'h.name', $param['name']]]);
        isset($param['id_code']) && $query->andWhere(['h.id_code' => $param['id_code']]);
        isset($param['mobile']) && $query->andWhere(['h.mobile' => $param['mobile']]);
      
        $sortArr = [];
        if (count($param['sort_list']) > 0) {
            foreach ($param['sort_list'] as $sort) {
                $sortArr[$sort->field] = ($sort->sort == "desc") ? SORT_DESC : SORT_ASC;
            }
            $query->orderBy($sortArr);
        } else {
            $query->orderBy(['f.created_on' => SORT_DESC,]);
        }

        if (isset($param["page"]) && isset($param['pageSize'])) {
            $pageSize = $param['pageSize'];
            $pageIndex = $param["page"];
            $offset = ($pageIndex - 1) * $pageSize;
            $query->offset($offset)->limit($pageSize);
        }

        $rows = $query->createCommand(PFan::getDb())->queryAll();
        if (count($rows) == 0) {
            return [];
        } 
        $member_id_arr = [];
        foreach ($rows as $row) { 
            if ($row["member_id"] != "") {
                $member_id_arr[] = $row['member_id'];
            }
        } 
//        $member_id_arr = array_unique($member_id_arr);
 

//        $memberIndex = 0;
//        if (count($member_id_arr)>0) {
//            while ($memberIndex <= count($member_id_arr)) {
//                $childArr = array_slice($member_id_arr, $memberIndex, 5000); 
//                $memberIndex += 5000;
//            }
//        } 
//
//        $type_arr = ['member' => '会员', 'fan' => '粉丝'];
//        $memberLevelRepository = new MemberLevelRepository();
//        $memberLevelList = $memberLevelRepository->getMemberLevelList();

        for ($i=0; $i<count($rows); $i++) {
            $rows[$i]['nick_name'] = $this->replaceLowOrderASCIICharacters($rows[$i]['nick_name']);
            $rows[$i]['id_type'] = $this->replaceLowOrderASCIICharacters($rows[$i]['id_type']);
            $rows[$i]['id_code'] = $this->replaceLowOrderASCIICharacters($rows[$i]['id_code']);
            $rows[$i]['mobile'] = $this->replaceLowOrderASCIICharacters($rows[$i]['mobile']);

            $rows[$i]['room_name'] = [];
            $rows[$i]['all_mobile'] = [];
            $rows[$i]['level'] = '';
//            if (isset($rows[$i]['member_id'])) {
//                $memberId = strtolower($rows[$i]['member_id']);
//                $rows[$i]['all_mobile'] = isset($mobile_list[$memberId]) && is_array($mobile_list[$memberId]) ? array_unique($mobile_list[$memberId]) : []; 
//                $rows[$i]['level'] = self::getMemberLevelById($memberLevelList, $rows[$i]['level_id']);
//            } 
            $rows[$i]['birthday'] && $rows[$i]['birthday'] = floor((time() - strtotime($rows[$i]['birthday'])) / (86400 * 365));
            $rows[$i]['follow_time'] = date('Y-m-d', strtotime($rows[$i]['follow_time']));
            $rows[$i]['created_on'] = date('Y-m-d', strtotime($rows[$i]['created_on']));
            $rows[$i]['first_login_time'] = empty($rows[$i]['first_login_time']) ? '' : date('Y-m-d', strtotime($rows[$i]['first_login_time']));
            $rows[$i]['user_type'] = self::_getUserType($rows[$i]);
            $rows[$i]['type'] = empty($rows[$i]['member_id']) ?'会员' : '粉丝';
        }
        return $rows;
    }

    private function replaceLowOrderASCIICharacters($nickName) {
        $newNickName = "";
        for($i=0;$i<strlen($nickName);$i++){
            $ord = ord($nickName[$i]);
            if ($i==0 && $ord == 61) {
                $newNickName .= " ".$nickName[$i];
            }
            elseif (($ord >= 0 && $ord <= 8) || ($ord >= 11 && $ord <= 12) || ($ord >= 14 && $ord <= 32)) {
                $newNickName .= " ";
            } else {
                $newNickName .= $nickName[$i];
            }
        }
        return $newNickName;
    }

    /**
     * 根据member_id 获取用户基本信息
     * @param type $member_id_arr
     * @return type
     */
    public function getMemberListByIds($member_id_arr)
    {
        $member_list = [];
        $query_select = (new Query())
            ->from(HMember::tableName())
            ->Where('is_deleted=0')
            ->andWhere(['in', 'id', $member_id_arr])
            ->select('id as member_id, id_type, level_id, name, type ,id_code, mobile,birthday ');
        $result = $query_select->createCommand(HMember::getDb())->queryAll();

        foreach ($result as $value) {
            $member_list[strtolower($value['member_id'])] = $value;
        }
        return $member_list;
    } 
}

