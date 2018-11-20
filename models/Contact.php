<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
/**
 * Contact is the model behind the Profile form.
 */
class Contact extends ActiveRecord
{    
    /**
     * @tableName
     */
    public static function tableName()
    {
        return 'contact';
    }	
	
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        $rules = [
            [['email', 'name', 'phone'], 'required'],			
            [['email'], 'email', 'message'=>'"E-mail" не є корректним'],			
		];
								
		return $rules;
    }
	
    /**
     * @attributeLabels
     */
    public function attributeLabels()
    {
        return [
            'phone' 		=> 'Телефон',
            'name' 			=> 'Імя',
            'email' 		=> 'E-mail',
        ];
    }
	
	public function beforeSave($insert)
	{
		if ($insert) {
			$this->create_time = time();
		}
 		
		return parent::beforeSave($insert);
	}
	
	public function sendEmail()
	{
		// https://login.sendpulse.com/settings/#api
		define( 'API_USER_ID', '7ca7ffc65864b2e408cac45b9ff09c02' );
		define( 'API_SECRET', '9f625e6ea01c160388464c7e057e348a' );

		define( 'TOKEN_STORAGE', 'file' );

		$api = new \app\components\SendPulse( API_USER_ID, API_SECRET, TOKEN_STORAGE );

		
		$data = [
			"Імя: {$this->name}", 
			"Телефон: {$this->phone}", 
			"E-mail: {$this->email}", 
		];
		
		// Send mail using SMTP
		$email = array(
			'html' => '<p>'.implode('<br/>', $data).'</p>',
			'text' => implode("\n", $data),
			'subject' => 'Контакт-форма',
			'from' => array(
				'name' => 'workmarketua',
				'email' => 'workmarketua@gmail.com'
			),
			'to' => array(
				array(
					'name' => 'workmarketua',
					'email' => 'workmarketua@gmail.com'
				)
			),
		);
		$api->smtpSendMail($email);
	}
				
}
