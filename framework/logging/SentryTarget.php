<?php

namespace app\framework\logging;

use Exception;
use Yii;
use yii\log;
use yii\log\Target;

class SentryTarget extends Target
{
    /**
     * @var string dsn for sentry access
     */
    public $dsn = '';

    /**
     * @var \Raven_Client client for working with sentry
     */
    protected $client = null;
    public $context = [];

    /**
     * Initializes the DbTarget component.
     * This method will initialize the [[db]] property to make sure it refers to a valid DB connection.
     * @throws InvalidConfigException if [[db]] is invalid.
     */
    public function init()
    {
        parent::init();
        require_once __DIR__ . '/raven/lib/Raven/Autoloader.php';
        \Raven_Autoloader::register();
        $options = ['curl_method' => 'exec'];//, sync,async,exec
        $options['log_vars'] = ['_COOKIE', '_SESSION', '_SERVER'];
        $options['message_limit'] = 1024 * 4;
        $this->client = new \Raven_Client($this->dsn, $options);
    }

    public function getContext()
    {
        return $this->context;
    }

    public function setContext($context)
    {
        if ($context == null) {
            $context = [];
        }
        $this->context = $context;
    }

    /**
     * Processes the given log messages.
     * This method will filter the given messages with [[levels]] and [[categories]].
     * And if requested, it will also export the filtering result to specific medium (e.g. email).
     * @param array $messages log messages to be processed. See [[Logger::messages]] for the structure
     * of each message.
     * @param boolean $final whether this method is called at the end of the current application
     */
    public function collect($messages, $final)
    {
        //生产环境不记录403, 404日志
        $myExcept = YII_ENV == 'prod' ? ['yii\web\HttpException:404', 'yii\web\HttpException:403', 'yii\base\InvalidRouteException'] : [];
        $exceptAll = array_merge($myExcept, $this->except);
        $this->messages = array_merge($this->messages, $this->filterMessages($messages, $this->getLevels(), $this->categories, $exceptAll));
        $count = count($this->messages);
        if ($count > 0 && ($final || $this->exportInterval > 0 && $count >= $this->exportInterval)) {
            $this->export();
            $this->messages = [];
        }
    }

    /**
     * Stores log messages to sentry.
     */
    public function export()
    {
        //sentry levels: fatal, error, warning, info, debug
        $levelArr = [
            \yii\log\Logger::LEVEL_ERROR => 'fatal',
            \yii\log\Logger::LEVEL_WARNING => 'warning',
            \yii\log\Logger::LEVEL_INFO => 'info',
            \yii\log\Logger::LEVEL_TRACE => 'debug',
            \yii\log\Logger::LEVEL_PROFILE_BEGIN => 'debug',
            \yii\log\Logger::LEVEL_PROFILE_END => 'debug',
        ];

        foreach ($this->messages as $message) {
            list($msg, $level, , , $traces) = $message;
            $levelName = isset($levelArr[$level]) ? $levelArr[$level] : 'unknown';
            $options = [
                'level' => $levelName,
                'extra' => [],
            ];

            $extData = [];
            if (is_array($msg)) {
                $errStr = isset($msg['msg']) ? $msg['msg'] : '';
                if (isset($msg['data'])) {
                    $options['extra'] = $msg['data'];
                }
            } elseif ($msg instanceof Exception) {
                $context = ['level'=> 'fatal'];
                $context['extra'] = $this->addContext([]);
                $this->client->captureException($msg, $context);
                return;
            } else {
                $errStr = $msg;
            }

            // Store debug trace in extra data
            $traces = array_map(
                function ($v) {
                    return "{$v['file']}" . PHP_EOL . "{$v['class']}::{$v['function']} [{$v['line']}]";
                },
                $traces
            );
            if (!empty($traces)) {
                $options['extra']['traces'] = $traces;
            }
            if ($levelName == 'fatal') {
                $context = isset($options['extra']) ? $options['extra'] : [];
                $options['extra'] = $this->addContext($context);
            }

            $this->client->captureMessage(
                $errStr,
                $extData,
                $options,
                false
            );
        }
    }

    public function addContext($context)
    {
        if (isset($this->context['session']) && $this->context['session']) {
            $session = \Yii::$app->session->get(\app\framework\auth\interfaces\UserSessionAccessorInterface::SESSION_KEY);
            $session = isset($session) ? clone $session : null;
            if ($session) {
                if (isset($session->db_dsn) && !empty($session->db_dsn)) {
                    if (is_array($session->db_dsn)) {
                        $session->db_dsn['pwd'] = '***';
                        $session->db_dsn['uid'] = '***';
                    } else {
                        $session->db_dsn->pwd = '***';
                        $session->db_dsn->uid = '***';
                    }

                }
                if (isset($session->db_master) && !empty($session->db_master)) {
                    if (is_array($session->db_master)) {
                        $session->db_master['pwd'] = '***';
                        $session->db_master['uid'] = '***';
                    } else {
                        $session->db_master->pwd = '***';
                        $session->db_master->uid = '***';
                    }
                }
                $context['user_session'] = json_encode($session, JSON_UNESCAPED_UNICODE);
            }
        }
        return $context;
    }
}
