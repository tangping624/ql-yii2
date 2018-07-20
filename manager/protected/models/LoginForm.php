<?php

namespace app\models;

use Yii;

use yii\base\Model;


class LoginForm extends Model
{
    public $userName;
    public $password;
    
    public $wxUserId;
    public $captchaCode;
    public $from = 1;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required 
            [['userName'], 'required', 'message' => '用户名不能为空'],
            [['password'], 'required', 'message' => '密码不能为空'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        //if (!$this->hasErrors()) {
        //    $user = $this->getUser();

        //    if (!$user || !$user->validatePassword($this->password)) {
        //        $this->addError($attribute, 'Incorrect username or password.');
        //    }
        //}
    }

}
