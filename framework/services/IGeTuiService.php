<?php
/**
 * Created by PhpStorm.
 * User: huanglc
 * Date: 2016/6/2
 * Time: 17:10
 */
namespace app\framework\services;
use app\framework\utils\DateTimeHelper;
use app\framework\utils\StringHelper;
require __DIR__.'/../3rd/getui/IGt.Push.php';

class IGeTuiService {

    private $appkey;
    private $appid;
    private $mastersecret;
    private $host;
    public $cid;

    private $_title = '明源移动承建商';
    private $igt = null;


    public function __construct($cid = null)
    {
        $settingAccessor = \Yii::$container->get('app\framework\settings\interfaces\SettingsAccessorInterface');
        $config = $settingAccessor->get('igetui_push');

        if (!isset($config)) {
            throw new \Exception('缺少配置项 igetui_push');
        }

        $config = json_decode($config);
        $this->appkey = $config->appkey;
        $this->appid = $config->appid;
        $this->mastersecret = $config->mastersecret;
        $this->host = $config->host;

        $this->cid = $cid;
        $this->_igt = new \IGeTui($this->host, $this->appkey, $this->mastersecret);
    }

    /**
     * 单个用户推送
     */
    public function pushMessageToSingle($data)
    {
        $template = $this->IGtNotificationTemplate($data);
        $message = new \IGtSingleMessage();

        $message->set_isOffline(true);
        $message->set_offlineExpireTime(3600*12*1000);
        $message->set_data($template);

        //接收方
        $target = new \IGtTarget();
        $target->set_appId($this->appid);
        $target->set_clientId($this->cid);

        $rep = $this->_igt->pushMessageToSingle($message,$target);
        $this->saveLog($rep, $data);
        return $rep;
    }

    /**
     * 多个用户推送
     */
    public function pushMessageToList($data)
    {
        putenv("gexin_pushList_needDetails=true");
        putenv("gexin_pushList_needAsync=true");

        $template = $this->IGtNotificationTemplate($data);
        //个推信息体
        $message = new \IGtListMessage();

        $message->set_isOffline(true);//是否离线
        $message->set_offlineExpireTime(3600*12*1000);//离线时间
        $message->set_data($template);//设置推送消息类型

        $contentId = $this->_igt->getContentId($message);

        $cids = is_array($this->cid) ? $this->cid : [$this->cid];
        $targetList = [];
        foreach ($cids as $cid) {
            $target = new \IGtTarget();
            $target->set_appId($this->appid);
            $target->set_clientId($cid);
            $targetList[] = $target;
        }

        $rep = $this->_igt->pushMessageToList($contentId, $targetList);
        $this->saveLog($rep, $data);
        return $rep;
    }

    /**
     * 多个用户
     */
    public function pushMessageToApp()
    {
        $template = $this->IGtNotificationTemplate(['title' => 'test3', 'content' => 'toapp', 'text' => 'successful send']);

        $message = new \IGtAppMessage();

        $message->set_isOffline(true);
        $message->set_offlineExpireTime(3600*12*1000);//离线时间单位为毫秒，例，两个小时离线为3600*1000*2
        $message->set_data($template);


        $message->set_appIdList(array($this->appid));
        $message->set_phoneTypeList(array('ANDROID'));
//	$message->set_provinceList(array('浙江','北京','河南'));
//	$message->set_tagList(array('开心'));

        $rep = $this->_igt->pushMessageToApp($message);
    }


    /**
     * @return IGtNotificationTemplate
     */
    public function IGtNotificationTemplate($data){
        $template =  new \IGtTransmissionTemplate();
        $template->set_appId($this->appid);
        $template->set_appkey($this->appkey);
        $template->set_transmissionType(1);
        $template->set_transmissionContent($data['transmissionContent']);

        $apn = new \IGtAPNPayload();
        $alertmsg = new \DictionaryAlertMsg();
        $alertmsg->body = "body";
        $alertmsg->actionLocKey = "ActionLockey";
        $alertmsg->locKey = $data['text'];
        $alertmsg->locArgs = array("locargs");
        $alertmsg->launchImage = "launchimage";

        $alertmsg->title = $data['text'];
        $alertmsg->titleLocKey = $this->_title;
        $alertmsg->titleLocArgs = array("TitleLocArg");
        $apn->alertMsg = $alertmsg;
        $apn->badge = 7;
        $apn->contentAvailable = 1;
        $apn->category = "ACTIONABLE";
        $template->set_apnInfo($apn);
        return $template;
    }

    /**
     * @return IGtTransmissionTemplate
     */
    public function IGtTransmissionTemplate()
    {
        $template = new IGtTransmissionTemplate();

        $template->set_appId($this->appid);

        $template->set_appkey($this->appkey);
        $template->set_transmissionType(1);
        $template->set_transmissionContent("测试离线");
        //$template->set_duration(BEGINTIME,ENDTIME); //设置ANDROID客户端在此时间内的展示信息
        //这是老方法iOS，新方法参见ios模板说明(PHP)*/
        //$template->set_pushInfo("actionLocKey","badge","message",
        //"sound","payload","locKey","locArgs","launchImage");
        return $template;
    }

    private function saveLog($result, $data)
    {
        $row = [
            'id' => StringHelper::uuid(),
            'user_id' => $data['user_id'],
            'client_id' => json_encode($this->cid),
            'type' => $data['type'],
            'message' => $data['text'],
            'created_on' => DateTimeHelper::now(),
            'is_pushed' =>  strtoupper($result['result']) == 'OK' ? '1' : '0',
            'error_cause' => strtoupper($result['result']) == 'OK' ? '' : $result['result']
        ];

        try {
            \Yii::$app->ContractorDb->createCommand()->insert('t_push_log', $row)->execute();
        } catch (\Exception $e) {
            
        }
    }
}