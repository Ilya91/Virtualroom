<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class LoginForm extends Model
{
    public $name;

    private $_user = false;
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['name'], 'required'],
        ];
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), 3600*24*30);
        }
        return false;
    }


    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = $this->name;
        }

        return $this->_user;
    }
}
