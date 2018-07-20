<?php
use \Mockery as m;
use yii\web\CookieCollection;

class ApplicationMocker
{
    public $app;
    private $_request;
    private $_response;
    private $_cookies;
    public $sessionMocker;
    public $cache;
    public $basePath;

    public function __construct($config)
    {
        $this->basePath = __DIR__ . '/../';

        $app = m::mock('yii\web\Application');
        \Yii::$app = $app;
        $app->id = $config['id'];//'testingAppId';

        $this->_cookies = new CookieCollection();
        $this->_cookies = new \yii\web\CookieCollection([], ['readOnly' => false]);

        $this->sessionMocker = $this->_mockSession();
        $this->_request = $this->_mockRequest();
        $this->_response = $this->_mockResponse();
        $this->cache = $this->_createCache();

        $app->shouldReceive('set');
        $app->shouldReceive('has')->andReturn(true);
        $app->shouldReceive('get')->with('request')->andReturn($this->_request);
        $app->shouldReceive('get')->with('response')->andReturn($this->_response);
        $app->shouldReceive('get')->with('session')->andReturn($this->sessionMocker);
        $app->shouldReceive('get')->with('cache')->andReturn($this->cache);
        $app->shouldReceive('get')->with('cache', false)->andReturn($this->cache);
        $app->shouldReceive('get')->with('cache', true)->andReturn($this->cache);

        $db = Yii::createObject($config['components']['db']);
        $app->shouldReceive('getDb')->andReturn($db);
        $app->shouldReceive('run');
        $this->app = $app;
    }

    private function _createCache()
    {
        $cache = new \yii\caching\FileCache(['cachePath'=>$this->basePath . '/runtime']);// \yii\caching\FileCache
        //  $cache->init();
        return $cache;
    }

    private function _mockRequest()
    {
        $request = m::mock('yii\web\Request');
        $request->shouldReceive('getCookies')->andReturn($this->_cookies);
        return $request;
    }

    private function _mockResponse()
    {
        $request = m::mock('yii\web\Response');
        $request->shouldReceive('getCookies')->andReturn($this->_cookies);
        return $request;
    }

    public function _mockSession()
    {
        $session = m::mock('yii\web\Session');
        return $session;
    }

}
