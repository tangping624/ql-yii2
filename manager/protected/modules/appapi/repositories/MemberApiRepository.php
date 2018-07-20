<?php 
namespace app\modules\appapi\repositories;
use app\modules\appapi\services\MemberApiService;
use app\modules\RepositoryBase;
use yii;
use app\entities\member\HMember;
class MemberApiRepository extends RepositoryBase
{
      /**
      * 检验会员是否存在
      * @param type $pdo
      * @param type $phone
      * @return boolean
      */
     public function checkUserIsExist($phone){
            $db = HMember::getDb();
            $sql = " select count(*) as count from h_member where mobile=:phone  and is_deleted=0 ";
            $params = [":phone"=>$phone];
            $count = $db->createCommand($sql,$params)->queryScalar() ;  
            if(intval($count)>0){
               return true;
            }
            return false;
     }
     /**
      * 保存用户
      * @param TUser $user
      * @return boolean
      */
     public function save(HMember $user){
         if(is_null($user)){
             return false;
         }
         return $user->save();
     }

    //验证登入密码
    public function findPwd($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        $db = HMember::getDb();
        $sql = " select pwd  from h_member where id=:id and is_deleted=0 ";
        $params = [":id"=>$id];
        $count = $db->createCommand($sql,$params)->queryOne() ;
        return $count['pwd'];
    }

    //更新密码
    public function stePwd($id, $password){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        $data=HMember::updateAll(array('pwd'=>$password),'id=:id',array(':id'=>$id));
        return $data;
    }

    //根据电话号码更新密码
    public function findUser($mobile,$password){
        if (empty($mobile)) {
            throw new \InvalidArgumentException('$mobile');
        }
        if (empty($password)) {
            throw new \InvalidArgumentException('$password');
        }
        $data=HMember::find()->where(['mobile'=>$mobile,'is_deleted'=>0])->select('id,pwd')->asArray()->one();
        if(empty($data)){
            return 66;
        }else {
            if($data['pwd']==md5($password)){
                return 88;
            }else{
                $param=[];
                $param[':mobile']=$mobile;
                $param[':pwd']=md5($password);
                $sql="update h_member set pwd=:pwd WHERE mobile=:mobile AND is_deleted=0";
                return $rst= HMember::getDb()->createCommand($sql, $param)->execute();
            }
            //return HMember::updateAll(array('pwd' => md5($password)), 'mobile=:mobile', array(':mobile' => $mobile));
        }
        /* $data=HMember::find()->where(['mobile'=>$mobile,'is_deleted'=>0])->select('id')->asArray()->one();
        if(empty($data)){
            return ['result' => false ,'code' => 500,'msg' =>'没有该用户',];
        }else{
            $data=HMember::updateAll(array('pwd'=>$password),'id=:id',array(':id'=>$data['id']));
            return ['result' => true  ,'code' => 200,'data' =>$data,'msg'=>'密码修改成功'];
        }*/
    }
    
    //验证用户绑定手机号
    public function findPhone( $mobile,$id){
        if (empty($mobile)) {
            throw new \InvalidArgumentException('$mobile');
        }
        $param=[];
        $param[':mobile']=$mobile;
        $param[':id']=$id;
        $sql="select id from h_member where mobile=:mobile and is_deleted=0 and id <>:id limit 1 ";
        $rst= HMember::getDb()->createCommand($sql, $param)->queryScalar();
        if($rst) {
            return true ;
        }
        return false;
    }

    //注册保存
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

    //验证原始密码
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

    //更新密码
    public function updatePwd($id, $npwd){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return  HMember::updateAll(array('pwd'=>md5($npwd)),'id=:id',array(':id'=>$id));
    }

    //修改图像
    public function updatePhoto($id,$headimg_url){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return  HMember::updateAll(array('headimg_url'=>$headimg_url),'id=:id',array(':id'=>$id));
    }

    //修改昵称
    public function updateName($id,$name){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return  HMember::updateAll(array('name'=>$name),'id=:id',array(':id'=>$id));
    }

    public function getUserInfo( $id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return HMember::find()->select('id,name,headimg_url')->where(['id'=>$id])->one();
    }

}
