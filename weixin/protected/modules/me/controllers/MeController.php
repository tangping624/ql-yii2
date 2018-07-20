<?php
namespace app\modules\me\controllers;
use app\controllers\ControllerBase;
use app\modules\me\services\MeService;
use yii\web\Cookie;
use app\framework\sms\SmsService;
use app\modules\pub\models\ListForm;
class MeController extends ControllerBase {
    private $_meService;
    public function __construct($id, $module, MeService $meService, $config = [])
    {
        $this->_meService = $meService;
        parent::__construct($id, $module, $config);
    }


    public function actionIndex()
    {        
        $user = $this->context->user;
        //var_dump($user);die;
        if(empty($user)){
            $this->redirect("/me/me/login-index");
        }

        $memu['navigation_list']= $this->getNavigation('home');
        $memu['menu']='me/me/index';
        $memu['public_id'] = $this->context->publicId;
        return  $this->render('index',['menu'=>$memu,'user'=>$user]);
    }

    public function actionLoginIndex(){

        return  $this->render('login');
    }

    //忘记密码

    public function actionForgetPwd(){

        return  $this->render('findpwd');
    }

    //立即注册

    public function actionRegister(){
        return  $this->render('register');
    }

    //验证码
    public function actionCheckCode($mobile='15172415261'){
        $sms=new SmsService();
        $code=$sms->sendVerifyCode($mobile);
        return $this->json($code);
    }

    //找回密码下一步
    public function actionForgetNext($mobile,$code){
        $sms=new SmsService();
        $rst=$sms->verifyCode($mobile,$code);
        if(!$rst['result']){
            return $this->json($rst);
        }
        return  $this->render('Findpsd2');
    }

    //更改密码
    public function actionChangePwd(){
        $mobile=$_POST['mobile'];
        $pwd=$_POST['pwd'];

            $rst=$this->_meService->changePwd($mobile,  $pwd);
            if($rst){
                $this->deleteCookie();
                return $this->json(['result' => true, 'code' => 200, 'msg' => '修改成功']);
            }else {
                return $this->json(['result' => false, 'code' => 500, 'msg' => '修改失败']);
            }

    }

    //登入
    public function actionLogin(){
        try {
        $mobile=$_POST['mobile'];
        $pwd=$_POST['pwd'];
        $re=$this->_meService->checkUser($mobile, $pwd);
        if($re){            
            $cookie = new Cookie();
            $cookie->name = 'u';
            $cookie->value = $re['mobile'];
            //\Yii::$app->response->cookies->remove($cookie);
            \Yii::$app->response->cookies->add($cookie);
            $cookie = new Cookie();
            $cookie->name = 'w';
            $cookie->value = $pwd;
            //\Yii::$app->response->cookies->remove($cookie);
            \Yii::$app->response->cookies->add($cookie);

            return $this->json(['result' => true, 'code' => 200, 'msg' => '登入成功']);
        }else{
            return $this->json(['result' => false, 'code' => 500, 'msg' => '登入失败']);
        }
        } catch (\Exception $ex) {
            return $this->json(['result' => false, 'code' => 500, 'msg' => $ex->getMessage()]);
        }
    }

    //注册下一步
    public function actionRegisterNext($mobile,$code){
        $sms=new SmsService();
        $rst=$sms->verifyCode($mobile,$code);
        if(!$rst['result']){
            return $this->json($rst);
        }
        return  $this->render('register2');
    }
    //注册
    public function actionSave($mobile='15172415261',$pwd='123456'){
        $re=$this->_meService->findUser($mobile);
        if($re) {
            $rst = $this->_meService->SaveUser($mobile, $pwd);
            if ($rst) {
                return $this->json(['result' => true, 'code' => 200, 'msg' => '注册成功']);
            } else {
                return $this->json(['result' => false, 'code' => 500, 'msg' => '注册失败']);
            }
        }else{
            return $this->json(['result' => false, 'code' => 500, 'msg' => '该用户已注册']);
        }

    }

    //我的
    public function actionMyInfo(){
        $this->context->memberId;
        $public_id = $this->context->publicId;
        $url = \Yii::$app->request->absoluteUrl;
        $wxjsdk = $this->_meService->getJssdksign($public_id, urldecode($url));
        $memberId=$this->context->memberId;
        $data= $re=$this->_meService->getUser( $memberId);
        return $this->render('myinfo',['data'=>$data,'wxjsdk'=>$wxjsdk]);

    }

    //修改图像
    public function actionUpdatePhoto($id='',$headimg_url=''){

        $rst = $this->_meService->updatePhoto($id,$headimg_url);
        if ($rst) {
            return $this->json(['result' => true, 'code' => 200, 'msg' => '修改成功']);
        } else {
            return $this->json(['result' => false, 'code' => 500, 'msg' => '修改失败']);
        }
    }

    //昵称页面
    public function actionName(){
        return  $this->render('changename');
    }
    //修改昵称
    public function actionUpdateName($id='',$name=''){
        $len=strlen($name);
        if($len<24) {
            $rst = $this->_meService->UpdateName($id, $name);
            if ($rst) {
                return $this->json(['result' => true, 'code' => 200, 'msg' => '修改成功']);
            } else {
                return $this->json(['result' => false, 'code' => 500, 'msg' => '修改失败']);
            }
        }else{
            return $this->json(['result' => false, 'code' => 500, 'msg' => '修改失败,最多输入24个字符']);
        }
    }

    //密码页面
    public function actionPwd(){
        return  $this->render('changepwd');
    }
    //修改密码
    public function actionUpdatePwd($id,$opwd,$npwd,$mobile){
        $re=$this->_meService->checkUser($mobile, $opwd);
        if($re) {
            $rst = $this->_meService->updatePwd($id, $npwd);
            if ($rst) {
              $this->deleteCookie();
                return $this->json(['result' => true, 'code' => 200, 'msg' => '修改成功']);
            } else {
                return $this->json(['result' => false, 'code' => 500, 'msg' => '修改失败']);
            }
        }else{
            return $this->json(['result' => false, 'code' => 500, 'msg' => '原登入密码不正确']);
        }
    }

    //退出登入
    public function actionLoginOut(){
        $this->deleteCookie();
        return $this->redirect("/me/me/login-index");
    }

    //删除cookie
    public function deleteCookie(){
        $cookies = \Yii::$app->response->cookies;
        $cookies->remove('u');
        $cookies->remove('w');
        unset($cookies['u']);
        unset($cookies['w']);
    }

    /**
     * 上传图片(base64 码)
     * @return bool|mixed
     */
   /* public function actionUploadImage()
    {
        if (!WebUtils::IsRequestParam('data')) {
            return $this->json(['result'=>false,'code' => INVALID_PARAMS, 'msg' => '未提base码[data]']);
        }
        var_dump($_FILES);exit;
        $imgData= $_REQUEST['data'];
        $base64_image=$imgData;
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/',  $base64_image, $result)){
            $fileName='image.jpg';
            $downloadPath = $_SERVER ['DOCUMENT_ROOT'] . '/temp/';//获取文件目录路径
            $extendName = strrchr($fileName, ".");//文件扩展名
            $actualName =uniqid() . $extendName;//新文件名称
            $filePath = $downloadPath . $actualName;//图片路径
            $filestring = base64_decode(str_replace($result[1], '', $base64_image));//解码
            $execResult=file_put_contents($filePath,  $filestring);
            if ($execResult === false) {
                return null;
            }
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
            return false;
        }

    }*/

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

