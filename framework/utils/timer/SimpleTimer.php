<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\framework\utils\timer; 
class SimpleTimer
{
    private $_startWatchTime = 0;
    private $_stopWatchTime = 0;
    private $_startTime = 0;
    private $_stopTime = 0;
    private $_timerId = '';
    private $_logger = null;
    private $_threshold = 0;
    
    public function __construct($timerId = '', $logger = null, $threshold = 0)
    {
        $this->_timerId = $timerId;
        $this->_logger = $logger;
        $this->_threshold = $threshold;
    }
    
    /**
     * 开始记时(整个实例仅一次)
     * @param type $remark
     */
    public function startWatch()
    {
        $start = microtime(true);
        $this->_startWatchTime = $start;
        $this->start();
    }
    
    /**
     * 停止记时并输出
     * @param type $remark
     */
    public function stopWatch($remark = '总耗时')
    {
        $this->_stopWatchTime = microtime(true);
        $watchElapsed = $this->spent($this->_startWatchTime, $this->_stopWatchTime);
        $this->log($watchElapsed, $remark);
        $this->endLog();
        return $watchElapsed;
    }
    
    /**
     * 开始记时
     * @param type $remark
     */
    public function start()
    {
        $time = microtime(true);
        $this->_startTime = $time;
    }
    
    /**
     * 重置
     */
    public function reset()
    {
        $this->_startTime = 0;
    }
    
    /**
     * 重置并开始计时
     * @param type $remark
     */
    public function restart()
    {
        $this->reset();
        $this->start();
    }
    
    /**
     * 停止记时
     * @param type $remark
     * @return type
     */
    public function stop($remark = '耗时')
    {
        $time = microtime(true);
        $this->_stopTime = $time;
        $elapsed = $this->spent($this->_startTime, $this->_stopTime);
        $this->log($elapsed, $remark);
        $this->start();
        return $elapsed;
    }
    
    private function spent($start, $end)
    {
        return round(($end - $start), 4);
    }
     
    private function log($time, $remark)
    {
        if (is_null($this->_logger) || $time < $this->_threshold) {
            return;
        }
        
        $content = $this->_timerId . ' ' . $remark . ' ' . $time . '秒';
        $this->_logger->log($content);
    }
    
    private function endLog()
    {
        if (is_null($this->_logger)) {
            return;
        }
        
        $this->_logger->end();
    }
}

