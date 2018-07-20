<?php

namespace app\framework\sms;


/**
 *
 * @property string $id
 * @property string $mobile
 * @property string $message
 * @property string $action_mark
 * @property string $send_time
 * @property integer $result
 * @property string $msg_ids
 */
class SmsSendLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sms_send_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['send_time'], 'safe'],
            [['result'], 'integer'],
            [['mobile'], 'string', 'max' => 20],
            [['message', 'msg_ids'], 'string', 'max' => 4000],
            [['action_mark'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '消息日志ID',
            'mobile' => 'Receive Mobile Tel',
            'message' => '消息',
            'action_mark' => '动作点标识',
            'send_time' => '发送时间',
            'result' => '发送结果',
            'msg_ids' => '短信IDs',
        ];
    }
}
