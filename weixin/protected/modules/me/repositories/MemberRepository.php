<?php
namespace app\modules\me\repositories;

use app\entities\member\HMember;

use app\modules\RepositoryBase;
use app\framework\db\PageResult;
use app\entities\merchant\Collection;
class MemberRepository extends RepositoryBase
{

    public function checkUser($mobile, $pwd){
        if (empty($mobile)) {
            throw new \InvalidArgumentException('$mobile');
        }
        if (empty($pwd)) {
            throw new \InvalidArgumentException('$pwd');
        }
        $sql = "select id,name,mobile,pwd,headimg_url from h_member where mobile=:mobile and pwd=:pwd and is_deleted=0";
        $params[':mobile']=$mobile;
        $params[':pwd'] = md5($pwd);
        $db = HMember::getDb();
        return $db->createCommand($sql,$params)->queryOne();


    }

    public function changePwd($mobile, $pwd){

        if (empty($mobile)) {
            throw new \InvalidArgumentException('$mobile');
        }
        if (empty($pwd)) {
            throw new \InvalidArgumentException('$pwd');
        }
        return  HMember::updateAll(array('pwd'=>md5($pwd)),'mobile=:mobile',array(':mobile'=>$mobile));


    }

    public function findUser($mobile){
        if (empty($mobile)) {
            throw new \InvalidArgumentException('$mobile');
        }
        $data=HMember::find()->where(['mobile'=>$mobile,'is_deleted'=>0])->one();
        if($data){
            return  0;
        }else{
            return 1;
        }

    }

    public function SaveUser($mobile, $pwd){
        if (empty($mobile)) {
            throw new \InvalidArgumentException('$mobile');
        }
        if (empty($pwd)) {
            throw new \InvalidArgumentException('$pwd');
        }
        $member=new HMember();
        $member->name=$mobile;
        $member->mobile=$mobile;
        $member->join_date=date('Y-m-d H:i:s');
        $member->pwd=md5($pwd);
        $member->created_on=date('Y-m-d H:i:s');
        $member->is_deleted=0;
        return $member->save();

    }

    public function getUser( $memberId){

        if (empty($memberId)) {
            throw new \InvalidArgumentException('$memberId');
        }
        return HMember::find()->where(['id'=>$memberId,'is_deleted'=>0])->asArray()->one();
    }

    public function updatePhoto($id,$headimg_url){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return  HMember::updateAll(array('headimg_url'=>$headimg_url),'id=:id',array(':id'=>$id));
    }

    public function updateName($id,$name){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return  HMember::updateAll(array('name'=>$name),'id=:id',array(':id'=>$id));
    }

    public function updatePwd($id, $npwd){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return  HMember::updateAll(array('pwd'=>md5($npwd)),'id=:id',array(':id'=>$id));
    }


}