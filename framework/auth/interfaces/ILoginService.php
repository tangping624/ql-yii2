<?php
namespace app\framework\auth\interfaces;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author Chenxy
 */
interface ILoginService 
{
    /**
     * 登入
     */
    public function login();
    
    /**
     * 登出
     */
    public function loginOut();
}
