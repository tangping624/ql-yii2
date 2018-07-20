<?php
 namespace app\modules\appapi\services;  
 use app\modules\appapi\services\AppServiceBase;
 use app\modules\appapi\repositories\TestRepository;
class TestService  extends AppServiceBase{  
    private  $_testRepostiory;
     public function __construct(TestRepository $testRepostiory)
    {
        $this->_testRepostiory = $testRepostiory; 
    }
    
    public function getAllUser(){
        return $this->_testRepostiory->getAllUser();
    }
}
