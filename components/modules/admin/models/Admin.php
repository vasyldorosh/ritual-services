<?php
namespace app\modules\admin\models;

use Yii;
use yii\web\IdentityInterface;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

class Admin extends \app\components\BaseModel implements IdentityInterface
{
	public $updateRelated = false;
	public $post_events = [];
	
    public $new_password = null;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%admin}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 100],
            ['email', 'trim'],
            ['email', 'unique', 'message' => 'Этот E-mail уже занят.'],                       
            [['name'], 'string', 'max' => 255],           
            [['group_id', 'is_active'], 'integer'],
            ['group_id', 'required'],            
            ['post_events', 'safe'],  
            ['new_password', 'required', 'on' => 'create'],
            ['new_password', 'string', 'min' => 6],
			
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'E-mail',
            'name' => 'Имя',
            'group_id' => 'Группа',
            'image' => 'Фото',
            'is_active' => 'Активность',
            'register_at' => 'Дата регистрации',
            'auth_at' => 'Дата авторизации',
            'auth_ip' => 'IP авторизации',
            'new_password' => 'Пароль',
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {		
		if ($insert || !empty($this->new_password))
        {
			$this->generateAuthKey();
            $this->setPassword($this->new_password);
        }		
		
		if ($insert) {
			$this->register_at = time();
		}
		
        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'is_active' => 1]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds admin by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'is_active' => 1,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $expire = Yii::$app->params['admin.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current admin
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
    
    public function getGroup()
    {
        return $this->hasOne(Group::className(), ['id' => 'group_id']);
    }
	
    /**
     * Finds admin by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByEmail($email)
    {
		$user = self::find()->joinWith('group')->where(['email' => $email])->one();
		
        return $user;
    }	
	
	public function afterSave($insert, $changedAttributes)
	{
		if ($this->updateRelated) {
			$savedEventIds = $this->getPost_events(true);
			
			if (!$insert) {
				AdminVsEvent::deleteAll('admin_id=' . $this->id);
			}
			
			$deletedIds = [];
			$addIds 	= [];
			
			foreach ($savedEventIds as $saved_id) {
				if (!in_array($saved_id, $this->post_events)) {
					$deletedIds[] = $saved_id;
				}
			}
			
			
			foreach ($this->post_events as $event_id) {
				if (empty($savedEventIds) || !in_array($event_id, $savedEventIds)) {
					$addIds[] = $event_id;
				}
				
				$vs = new AdminVsEvent;
				$vs->admin_id = $this->id;
				$vs->event_id = $event_id;
				$vs->save();
			}
			
			//save log
			foreach ($deletedIds as $event_id) {
				$log = new AdminVsEventLog;
				$log->admin_id = $this->id;
				$log->event_id = $event_id;
				$log->action = 'delete';
				$log->save();
			}
			foreach ($addIds as $event_id) {
				$log = new AdminVsEventLog;
				$log->admin_id = $this->id;
				$log->event_id = $event_id;
				$log->action = 'add';
				$log->save();
			}
		}
		
		//post_events
		
		return parent::afterSave($insert, $changedAttributes);
	}
	
	public function getPost_events($from_db=false) {
		
		if ($this->isNewRecord) {
			return [];
		} else {
			if (!Yii::$app->request->isPost || $from_db) {
				
				return Yii::$app->db->createCommand("SELECT event_id FROM ".AdminVsEvent::tableName()." WHERE admin_id= {$this->id}")->queryColumn();
			
			} else {
				return $this->post_events;
			}
		}
		
	}
	
    public static function getList()
    {
        return ArrayHelper::map(self::find()->all(),'id','name_email');
    }	
	
	public function getName_email()
	{
		return "{$this->name}  <{$this->email}>";
	}
	
}