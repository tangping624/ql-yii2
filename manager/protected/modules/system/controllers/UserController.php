<?php
namespace app\modules\system\controllers;
use app\modules\ControllerBase; 
use app\modules\system\services\UserService; 
use app\entities\TUser;
use yii;
use yii\base\ErrorException;
use yii\base\InvalidParamException;
use yii\web\Response;
use \yii\db\ActiveRecord; 
use app\framework\utils\Security;
use app\framework\utils\StringHelper;
use app\framework\utils\DateTimeHelper; 
  use app\services\AccountService;
class UserController extends ControllerBase {
    private  $_userService;
    private $_accountService;
    private $_popupLayout = '../../../../views/layouts/popup.php';
    public function __construct($id, $module, UserService $userserice,AccountService $accountService, $config = [])
    {
        $this->_userService = $userserice;
            $this->_accountService = $accountService;
        parent::__construct($id, $module, $config);
    }
    
     /** 新增用户action
     * @param $oid
     * @return string
     */
    public function actionAdd()
    {  
        $this->layout = $this->_popupLayout; 
        return $this->render('add' );
    }
    
    /**
     * 保存用户数据action
     * @return Array json
     * */
    public function actionSave()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $status = false;

        $post = yii::$app->request->post();
        if (empty($post['name'])) {
            return ['status' => $status, 'message' => '参数无效name'];
        }

        if (empty($post['account'])) {
            return ['status' => $status, 'message' => '参数无效account'];
        } else {
            //校验是否有重复账号
            if (TUser::find()->where(['account' => $post['account'], 'is_deleted' => 0])->count() > 0) {
                return ['status' => $status, 'message' => '该帐号已经存在'];
            }
        }

        if (empty($post['mobile'])) {
            return ['status' => $status, 'message' => '参数无效mobile'];
        } else {
            //校验邮箱是否已被注册
            if (TUser::find()->where(['mobile' => $post['mobile'], 'is_deleted' => 0])->count() > 0) {
                return ['status' => $status, 'message' => '该手机号已被注册'];
            }
        } 
        $plaintext = '123456'; 
        $entryText = Security::encryptByPassword($plaintext);
        
        //构造一个用户实体
        $userEntity = new TUser();
        $post['enabled'] = 1;
        $post['pwd'] = $entryText;
        self::loadFormData($userEntity, $post);
        $userEntity->id = StringHelper::uuid();  
        
        //提交数据
        $status = $this->_userService->insert($userEntity);  
        return self::getJsonResult($status, '保存');
    }
    
     /** 修改用户信息
     * @param $oid
     * @return string
     */
    public function actionEdit($oid)
    {
        $this->layout = $this->_popupLayout;
        $userEntity = TUser::find()->where(['id' => $oid])->one();   
        return $this->render('edit', ["user" => $userEntity]);
    }

    /**
     *功能：更新用户信息
     * @return Array json对象数组
     */
    public function actionUpdate()
    {
        $post = yii::$app->request->post(); 
        $id = $post['id'];

        $userEntity = TUser::find()->where(['id' => $id])->one();
        if ($userEntity == null) {
            return self::getJsonResult(false, '保存', '当前用户不存在');
        } 
        //校验是否有重复账号
        if (TUser::find()->where(['account' => $post['account'], 'is_deleted' => 0])->andWhere('id <> \'' . $userEntity->id . '\' ')->count() > 0) {
            return self::getJsonResult(false, '保存', '该帐号已经存在');
        }
        //校验邮箱是否已被注册
        if (TUser::find()->where(['mobile' => $post['mobile'], 'is_deleted' => 0])->andWhere('id <> \'' . $userEntity->id . '\' ')->count() > 0) {
            return  self::getJsonResult(false, '保存', '该手机号已被注册'); 
        } 

        //加载post过来的数据
        self::loadFormData($userEntity, $post);  
     
        $result = $this->_userService->update($userEntity);
        return self::getJsonResult($result, '保存');
    }
    
    
    /**
     * 功能：禁用用户
     * @return string
     */
    public function actionDisable()
    {
        $status = false;
        $id = yii::$app->request->get('id');
        if (empty($id)) {
            return self::getJsonResult($status, '禁用', '参数错误');
        }

        $userEntity = TUser::find()->where(['id' => $id])->one();
        if (empty($userEntity)) {
            return self::getJsonResult($status, '禁用', '当前用户不存在');
        }

        if ($userEntity['enabled'] != 1) {
            return self::getJsonResult($status, '禁用', '当前用户已经被禁用');
        }

        $userEntity['enabled'] = 0;
        $status = $this->_userService->update($userEntity);
        return self::getJsonResult($status, '禁用');
    }

    /**
     * 功能：删除用户
     * @return string Json
     */
    public function actionDelete()
    {
        $status = false;
        $id = yii::$app->request->get('id');
        if (empty($id)) {
            return self::getJsonResult($status, '删除', '参数错误');
        }
        $userEntity = TUser::find()->where(['id' => $id])->one();
        if (empty($userEntity)) {
            return self::getJsonResult($status, '删除', '当前用户不存在');
        }

        if ($userEntity['is_deleted'] == 1) {
            return self::getJsonResult($status, '删除', '当前用户已经被删除');
        }

        $userEntity['is_deleted'] = 1;
        $status = $this->_userService->update($userEntity);
        return self::getJsonResult($status, '删除');
    }

   
    /**
     * 功能：启用用户
     * @return string json
     */
    public function actionEnable()
    {
        $status = false;
        $id = yii::$app->request->get('id');
        if (empty($id)) {
            return self::getJsonResult($status, '启用', '参数错误');
        }

        $userEntity = TUser::find()->where(['id' => $id])->one();
        if (empty($userEntity)) {
            return self::getJsonResult($status, '启用', '当前用户不存在');
        }

        if ($userEntity['enabled'] == 1) {
            return self::getJsonResult($status, '启用', '当前用户已经被禁用');
        }

        $userEntity['enabled'] = 1;
        $status = $this->_userService->update($userEntity);
        return self::getJsonResult($status, '启用');
    }

  
    /**
     * 修改用户密码
     * @return array
     */
    public function actionChgpassword($oid)
    {  $this->layout = $this->_popupLayout;
        $paramData['id']=$oid;
        return $this->render('chgpassword',$paramData);
    }
       /**
     * 修改用户密码
     * @return array
     */
    public function actionPassword()
    {  
        $user = $this->sessionAccessor->getUserSession();
        $userInfo = $this->_userService->getUserInfo($user->user_id);
        $data = [];
        $data["user_info"] = $userInfo;
        return $this->render('password', ['data' => $data]);
    }
     /**
     * 修改密码
     * @return mixed
     */
    public function actionUpdatePassword()
    {
        try {
            $user = $this->sessionAccessor->getUserSession();
            $userId = $user->user_id;
            $oldPassword = $this->request->post("old_password");
            $newPassword = $this->request->post("new_password");
            if (empty($userId) || empty($oldPassword) || empty($newPassword)) {
                return $this->json(['status' => 0, 'code' => 200, 'msg' => '参数不能为空!']);
            }

            $match = $this->_accountService->validateUser($userId, $oldPassword);
            if ($match == false) {
                return $this->json(['status' => 0,'code' => 200,  'msg' => '密码不正确!']);
            } else {
                $result = $this->_accountService->updatePassword($userId, $newPassword);
                if ($result > 0) {
                    return $this->json(['status' => 1,'code' => 200,  'msg' => '修改成功!']);
                } else {
                    return $this->json(['status' => 0,'code' => 200,  'msg' => '更新数据记录失败!']);
                }
            }  
        } catch (\Exception $ex) {
            return $this->json(['status' => 0, 'code' => 500, 'msg' => $ex->getMessage()]);
        }
    }
 public function actionSavepassword($id)
    {
        $password = yii::$app->request->post('password');
        if (empty($password)) {
            return $this->jsonData(false, '参数无效');
        } else {
            try {
                $resetResult = $this->_userService->chgPassword($id,$password);
                $result = $resetResult['result'];  
            } catch (InvalidParamException $ipe) {
                return $this->jsonData(false, $ipe->getMessage());
            } catch (\Exception $e) {
                yii::error(json_encode($e));
                return $this->jsonData(false, '处理失败，请稍后再试');
            }
        } 
        return $this->jsonData($result, '修改密码' . ($result ? '成功' : '失败'));
    }
    
    
      public function actionIndex(){
         return $this->render('index');
    } 
       /**
     * 获取用户列表数据
     * @param int $page 当前页
     * @param int $pageSize 数据行
     * @return Array JSON
     */
    public function actionUserList($page = 1, $pageSize = 10)
    { 
        $isenable=  yii::$app->request->get('enabled');
        $userinfo=yii::$app->request->get('userinfo'); 
        $result = $this->_userService->getUserList($page, $pageSize, $isenable,$userinfo);
        return $this->json($result);
    } 
    
    /**
     * 根据账号或名称搜索用户
     * @param int $top 取前几条数据，默认为10
     * @return Array JSON
     */
    public function actionTopSearch($top = 10,$level=3)
    {
        $condition = [];
        $condition['t_user.is_deleted'] = 0;
        $condition['t_user.enabled'] = 1;
        $condition['t_ugroup.level'] = $level;
        $keyword = empty($_POST['keyword']) ? '' : '%' . $_POST['keyword'] . '%'; 
        $allUser = TUser::find()
                ->innerJoin('t_ugroup','t_user.group_id=t_ugroup.id')
            ->where($condition)
            ->andWhere(' t_user.`name` like :keyword or t_user.`account` like :keyword', [':keyword' => $keyword])
            ->limit($top)
            ->select('t_user.`id`,t_user.`name`,t_user.`account`')
            ->orderBy(['t_user.account' => SORT_ASC])
            ->all();
        return $this->json($allUser);
    }
     /** 公众号中新增用户
     * @param $level
     * @return string
     */
    public function actionAddAccount($level,$accountId)
    {
        $this->layout = $this->_popupLayout; 
        $ugroup = $this->_userService->getAllUGroup(); 
        return $this->render('addaccount', ["level" => $level,'account_id'=>$accountId,'ugroups'=>$ugroup]);
    }
      
    
    /**
     * 获取用户列表数据
     * @param int $page 当前页
     * @param int $pageSize 数据行
     * @return Array JSON
     */
    public function actionUserListAccount($page = 1, $pageSize = 100,$level=3)
    { 
         $isenable=  yii::$app->request->get('enabled'); 
        $result = $this->_userService->getUserListAccount($page, $pageSize,$level);
        return $this->json($result); 
    }  
     
        /**
     * 获得前端json结果数组
     * @param boolean $result 业务是否成功
     * @param string $businessType 业务类型
     * @param String $errorMsg 错误信息
     * @return Array
     */
    public static function getJsonResult($result, $businessType, $errorMsg = '')
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $errorMsg = $errorMsg == '' ? ($businessType . ($result ? '成功' : '失败')) : $errorMsg;
        return ['status' => $result, 'message' => $errorMsg];
    }
     /**
     * 功能：将array数据添加到实体当中
     * @param ActiveRecord $obj 数据库实体记录对象
     * @param Array $values 需要加载值数组
     * @return void
     */
    public static function loadFormData(ActiveRecord $obj, $values)
    {
        $attributes = $obj->getAttributes();
        foreach ($values as $name => $value) {
            if (array_key_exists($name, $attributes)) {
                $obj->$name = $value;
            }
        }
    }
    
}
