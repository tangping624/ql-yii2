<?php
/**
 * Created by hwl on 15-11-13.
 */

namespace app\framework\queue;

use yii\base\Component;

/**
 * @property string $host
 * @property string $port
 * @property int $database
 */
class Client extends Component
{
    protected $host;
    protected $port;
    protected $uid;
    protected $pwd;
    protected $database;

    public function __construct($config = [])
    {
        //register class autoload
        \app\framework\queue\Autoloader::register();
        parent::__construct($config);
    }

    public function init()
    {
        if (empty($this->host)) {
            throw new \InvalidArgumentException('$host没有配置');
        }
        if (empty($this->port) || !is_int($this->port)) {
            throw new \InvalidArgumentException('$port必须配置整数');
        }
        if (!empty($this->uid) && !empty($this->pwd)) {
            $server = "unix://{$this->uid}:{$this->pwd}@{$this->host}:{$this->port}";
        } else {
            $server = "unix://{$this->host}:{$this->port}";
        }
        \Resque::setBackend($server, $this->database);
    }

    public function setHost($host)
    {
        $this->host = $host;
    }

    public function setPort($port)
    {
        $this->port = $port;
    }

    public function setDatabase($database)
    {
        $this->database = $database;
    }

    public function setUid($uid)
    {
        $this->uid = $uid;
    }

    public function setPwd($pwd)
    {
        $this->pwd = $pwd;
    }

    /**
     * @param string $queue queue name
     * @param string $jobName
     * @param mixed $args 参数
     * @param bool|false $trackStatus Set to true to be able to monitor the status of a job.
     * @return string
     */
    public function enque($queue, $jobName, $args = null, $trackStatus = false)
    {
        if(empty($queue)) {
            throw new \InvalidArgumentException('$queue 不能为空');
        }
        if(empty($jobName)) {
            throw new \InvalidArgumentException('$jobName 不能为空');
        }

        return \Resque::enqueue($queue, $jobName, $args, $trackStatus);
    }

}
