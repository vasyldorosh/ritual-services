<?php
 
namespace app\commands;
 
use Yii;
use yii\console\Controller;
use app\modules\event\models\Event; 
use app\modules\event\models\EventSpool; 
use app\components\Event as EventComponent; 
use app\modules\settings\models\Settings;
 
/**
 * Event controller
 */
class EventController extends Controller {
 
	/**
	 * Кол-во писем, обрабатываемых за один проход
	 */
	const LETTERS_COUNT_PER_MOMENT = 100; 
 
	public function actionSend()
	{
		$configValues = Settings::getInstance()->getData();
		
		$letters = EventSpool::find()
			->where(['status'=>EventComponent::SPOOL_STATUS_AWAIT])
			->limit(self::LETTERS_COUNT_PER_MOMENT)
			->all();
		
		$events = Event::getData();
		
		//если что-то выбрали, обрабатываем
		if (is_array($letters) && count($letters)) {
			
			//инициируемся
			$email = Yii::$app->email;
				
			//просматриваем все полученные из таблицы и готовые к отправке письма
			foreach ($letters as $letter) {
				
				//разсериалайзим мыла для параметра to
				$email->to = $letter->email_to;
				
				$robot 		= !empty($events[$letter->event->id]['from_email']) ? $events[$letter->event->id]['from_email'] : $configValues['event_from_email'];
				$fromName 	= !empty($events[$letter->event->id]['translations'][$letter->lang]['from_name']) ? $events[$letter->event->id]['translations'][$letter->lang]['from_name'] : $configValues['event_from_name'];
				
				$email->from = EventComponent::mimeHeaderEncode($fromName).sprintf('<%s>', $robot);
				
				$email->replyTo = $robot;

				//устанавливаем тему письма
				$email->subject = EventComponent::mimeHeaderEncode($letter->subject);
				
				//устанавливаем тело
				$email->message = preg_replace('/\n/', '<br />', html_entity_decode($letter->content));
				
				//апдейтим записи статус и устанавливаем время отправки письма
				$status = EventComponent::SPOOL_STATUS_FAILURE;
				if ($email->send()) { $status = EventComponent::SPOOL_STATUS_DELIVERED;}
				
				$sql = sprintf('UPDATE `event_spool` SET status = %d, send_at = %d WHERE id = %d', $status, time(), $letter->id);
				
				//сохраняем
				Yii::$app->db->createCommand($sql)->execute();
			}
		}
	}
	


}
 