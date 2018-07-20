<?php

namespace app\models;

use Yii;

use app\framework\web\extension\FormBase;


class LoginForm extends FormBase
{
    public $userName;
    public $password;

    private $_user;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['userName', 'password'], 'required', 'message' => '帐号密码不能为空'],
            ['password', 'validatePassword'],
        ];
    }

}
