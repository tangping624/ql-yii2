<?php

namespace app\framework\entities;

use app\framework\db\EntityBase;

class T_user extends EntityBase implements \yii\web\IdentityInterface
{

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        //return static::findOne(['Password' => $token]);
        return static::findOne(['Password' => $token]);
    }

    /**
     * Finds user by username
     *
     * @param  string      $username
     * @return static|null
     */
    public static function findByUserName($username, $password)
    {
        return static::findOne(['UserCode' => $username, 'Password' => $password]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->UserGUID;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->Password;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->Password === $authKey;
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return $this->Password === $password;
    }
}
