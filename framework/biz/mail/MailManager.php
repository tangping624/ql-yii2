<?php
/**
 * 邮件生成器管理
 * User: robert
 * Date: 2015/4/30
 * Time: 15:46
 */
namespace app\framework\biz\mail;

use app\framework\settings\SettingsAccessor;

/**
 * 邮件生成器
 */
class MailManager
{

    /**
     * @inheritdoc
     */
    public static function sendMail($to, $subject, $content)
    {
        $settingsAccessor = new SettingsAccessor();
        $config = $settingsAccessor->get("email_config");
        $config = json_decode($config);

        $mailer = \Yii::$app->mailer;
        $transpport = [
            'class' => $config->class,
            'host' => $config->host,
            'username' => $config->username,
            'password' => $config->password,
            'port' => $config->port,
            'encryption' => $config->encryption,
        ];
        $mailer->setTransport($transpport);

        return $mailer->compose($config->template, ['model' => ['subject' => $subject, 'content' => $content]])
            ->setFrom($config->adminEmail)
            ->setTo($to)
            ->setSubject($subject)
            ->send();
    }
}
