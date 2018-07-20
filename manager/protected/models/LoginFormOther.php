<?php 
namespace app\models;

use Yii;

use yii\base\Model;


class LoginFormOther extends Model
{
    public $openid; 
    public $authinfo;
    public $type;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required 
            [['openid'], 'required', 'message' => '登陆Openid不能为空'],
            [['type'], 'required', 'message' => '登陆类型不能为空'],
            [['authinfo'], 'required', 'message' => '登陆信息不能为空'],
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

 