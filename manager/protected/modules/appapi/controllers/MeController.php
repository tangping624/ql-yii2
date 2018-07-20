<?php
namespace app\modules\appapi\controllers;
use app\modules\appapi\services\MemberApiService;
use app\modules\appapi\utils\WebUtils;
use app\framework\sms\SmsService;
use yii\web\Cookie;
class MeController extends AppControllerBase{

    private  $_memberApiService;
    private $_smsService;

    public function __construct($id, $module,MemberApiService $memberApiService,SmsService $smsService,   $config = [])
    {
        $this->_memberApiService = $memberApiService;
        $this->_smsService = $smsService;
        parent::__construct($id, $module, $config);
    }

    //发送验证码
    public function actionSendverifycode(){
        if (!WebUtils::IsRequestParam('mobile')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提手机号码[mobile]']);
        }
        $mobile = $_REQUEST['mobile'];
        $return = $this->_memberApiService->sendVerifyCode($mobile);
        return $this->json($return);
    }

    //忘记密码发送验证码
    public function actionForgetSendverifycode(){
        if (!WebUtils::IsRequestParam('mobile')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提手机号码[mobile]']);
        }
        $mobile = $_REQUEST['mobile'];
        $model=$this->_memberApiService->checkUserIsExist($mobile);
        if($model){
            $return = $this->_memberApiService->sendVerifyCode($mobile);
            return $this->json($return);
        }else{
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '该手机号码未注册，请先去注册！']);
        }
    }

    //检验手机验证码
    public function actionVerifycode(){
        if (!WebUtils::IsRequestParam('mobile')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提手机号码[mobile]']);
        }
        $mobile = $_REQUEST['mobile'];
        if (!WebUtils::IsRequestParam('verifycode')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提手机号码验证码[verifycode]']);
        }
        $inputcode = $_REQUEST['verifycode'];
        $return =   $this->_memberApiService->VerifyCode($mobile,$inputcode);
        return $this->json($return);
    }

    //校验手机号码
    public function actionCheckphone(){
        if (!WebUtils::IsRequestParam('mobile')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提手机号码[mobile]']);
        }
        $mobile = $_REQUEST['mobile'];
        if($this->_memberApiService->checkUserIsExist($mobile)){
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '该手机号码已注册，请更换手机号！']);
        }
        return  $this->json(['result'=>true,'code' =>'200', 'msg' => '']);
    }

    //注册
    public function actionSave(){
        if (!WebUtils::IsRequestParam('mobile')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提手机号码[mobile]']);
        }
        $mobile = $_REQUEST['mobile'];
        if (!WebUtils::IsRequestParam('pwd')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提密码[pwd]']);
        }
        $pwd = $_REQUEST['pwd'];
        if($this->_memberApiService->checkUserIsExist($mobile)){
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '该手机号码已注册，请更换手机号！']);
        }
        $rst = $this->_memberApiService->SaveUser($mobile, $pwd);
        if ($rst) {
            return $this->json(['result' => true, 'code' => 200, 'msg' => '注册成功']);
        } else {
            return $this->json(['result' => false, 'code' => 500, 'msg' => '注册失败']);
               }
        }

    //忘记密码修改密码
    public function actionForgetPwd()
    {
        if (!WebUtils::IsRequestParam('mobile')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提手机号码[mobile]']);
        }
        $mobile = $_REQUEST['mobile'];
        if (!WebUtils::IsRequestParam('pwd')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提密码[pwd]']);
        }
        $pwd = $_REQUEST['pwd'];
        $rst = $this->_memberApiService->findUser($mobile,$pwd);
        if ($rst==66) {
            return $this->json(['result' => false, 'code' => 500, 'msg' => '没有该用户']);
        } elseif($rst==88) {
            return $this->json(['result' => false, 'code' => 500, 'msg' => '与原始密码重复']);
        }else{
            return $this->json(['result' => true, 'code' => 200, 'msg' => '修改成功']);
        }
    }

    //修改密码
    public function actionUpdatePwd()
    {
        if (!WebUtils::IsRequestParam('member_id')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提member_id[member_id]']);
        }
        $member_id = $_REQUEST['member_id'];
        if (!WebUtils::IsRequestParam('opwd')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提原始密码[opwd]']);
        }
        $opwd = $_REQUEST['opwd'];
        if (!WebUtils::IsRequestParam('mobile')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提手机号码[mobile]']);
        }
        $mobile = $_REQUEST['mobile'];
        if (!WebUtils::IsRequestParam('npwd')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提新密码[npwd]']);
        }
        $npwd = $_REQUEST['npwd'];
        $re=$this->_memberApiService->checkUser($mobile, $opwd);
        if($re) {
            $rst = $this->_memberApiService->updatePwd($member_id, $npwd);
            if ($rst) {
                return $this->json(['result' => true, 'code' => 200, 'msg' => '修改成功']);
            } else {
                return $this->json(['result' => false, 'code' => 500, 'msg' => '修改失败']);
            }
        }else{
            return $this->json(['result' => false, 'code' => 500, 'msg' => '原登入密码不正确']);
        }
    }
    

    //修改图像
    public function actionUpdatePhoto()
    {
        //var_dump($_REQUEST);die;
        //var_dump($_GET);die;
        if (!WebUtils::IsRequestParam('member_id')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提member_id[member_id]']);
        }
        $member_id = $_REQUEST['member_id'];
        if (!WebUtils::IsRequestParam('headimg_url')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提图片地址[headimg_url]']);
        }
        $headimg_url = $_REQUEST['headimg_url'];
        $rst = $this->_memberApiService->updatePhoto($member_id,$headimg_url);
        if ($rst) {
            return $this->json(['result' => true, 'code' => 200, 'msg' => '修改成功']);
        } else {
            return $this->json(['result' => false, 'code' => 500, 'msg' => '修改失败']);
        }
    }
    
    //修改昵称
    public function actionUpdateName()
    {
        if (!WebUtils::IsRequestParam('member_id')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提member_id[member_id]']);
        }
        $member_id = $_REQUEST['member_id'];
        if (!WebUtils::IsRequestParam('name')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提昵称[name]']);
        }
        $name = $_REQUEST['name'];
        $len=strlen($name);
        if($len<24) {
            $rst = $this->_memberApiService->updateName($member_id, $name);
            if ($rst) {
                return $this->json(['result' => true, 'code' => 200, 'msg' => '修改成功']);
            } else {
                return $this->json(['result' => false, 'code' => 500, 'msg' => '修改失败']);
            }
        }else{
            return $this->json(['result' => false, 'code' => 500, 'msg' => '修改失败,最多输入24个字符']);
        }
    }

    //我的个人信息
    public function actionMyInfo(){
        if (!WebUtils::IsRequestParam('member_id')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提member_id[member_id]']);
        }
        $member_id = $_REQUEST['member_id'];
        $data = $this->_memberApiService->getUserInfo( $member_id);
        return $this->json($data);

    }

    public function actionUploadImage()
    {
        ini_set('memory_limit','384M');
        $fileName='image.jpg';
        $downloadPath = $_SERVER ['DOCUMENT_ROOT'] . '/temp/';//获取文件目录路径
        $extendName = strrchr($fileName, ".");//文件扩展名
        $actualName =uniqid() . $extendName;//新文件名称
        $filePath = $downloadPath . $actualName;//图片路径
        $re=move_uploaded_file($_FILES['file']['tmp_name'],   $filePath);
        if($re) {
            $file = [];
            $file["name"] = $actualName;
            $file['tmp_name'] = $filePath;
            try {
                $imgServ = new \app\framework\services\ImageService();
                $thumbnail_size = \Yii::$app->params['thumbnail_size'];
                $result = $imgServ->upload($file, 'appimage', true, $thumbnail_size);
            } catch (\Imagine\Exception\RuntimeException $ex) {
                \Yii::error($ex->getMessage());
                if (strstr($ex->getMessage(), 'Unable to open image')) {
                    $result = ['status' => 0, 'original' => null, 'thumbnail' => null, 'msg' => 'Image is not complete'];
                } else {
                    throw $ex;
                }
            }
            return $this->json($result);
        }else{
            return $this->json(['result' => false, 'code' => 500, 'msg' => '图片上传失败']);
        }

    }

    
}
