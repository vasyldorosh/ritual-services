<?php

namespace app\modules\admin\models;

use Yii;
use yii\base\Model;

/**
 * RemindForm is the model behind the login form.
 */
class RemindForm extends Model
{
    public $email;
    public $rememberMe = true;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // email and password are both required
            [['email'], 'required'],
            [['email'], 'email'],
        ];
    }
	
	public function afterValidate()
	{
		if (!$this->hasErrors()) {
			$user = $this->getUser();
			
			if (empty($user)) {
				$this->addError('email', 'E-mail не найден');
			} else if (!$user->is_active) {
				$this->addError('email', 'Пользователь не активен');
			} else if (empty($user->group) || (!empty($user->group) && !$user->group->is_active)) {
				$this->addError('email', 'Группа пользователя не активна');
			}
		}
	}
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => 'E-mail',
        ];
    }

    /**
     * Finds user by [[email]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = Admin::findByEmail($this->email);
        }

	    return $this->_user;
    }
}
