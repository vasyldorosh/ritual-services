<?php 
namespace app\components;
use Yii;
use yii\base\Component;
use app\modules\settings\models\Settings;
use app\modules\event\models\EventSpool; 
use app\modules\event\models\Event as EventModel; 

class Event extends Component
{	
	const CONTENT_TYPE_HTML 		= 1;
	const CONTENT_TYPE_TEXT_PLAIN 	= 2;

	const SPOOL_STATUS_AWAIT 		= 1;
	const SPOOL_STATUS_DELIVERED 	= 2;
	const SPOOL_STATUS_FAILURE 		= 3;
	
	/**
	 * Шаблон для переменных в письме
	 */
	const VAR_TEMPLATE = '/\[%s\]/i';	
	
    /**
     * @return array
     */
    public static function getList()
    {
		$list = [];
		
		foreach (\Yii::$app->params['events'] as $event_id=>$data) {
			$list[$event_id] = $data['title'];	
		}
		
		return $list;
    }	
	
    /**
     * @return string
     */	
	public static function getTitle($event_id)
    {
		$list = self::getList();
		
		if (isset($list[$event_id])) {
			return $list[$event_id];
		} else {
			return '';
		}
    }	
	
    /**
     * @return string
     */	
	public static function getVars($event_id)
    {
		if (isset(\Yii::$app->params['events'][$event_id]['vars'])) {
			$vars = explode(',', \Yii::$app->params['events'][$event_id]['vars']);
			
			$items = [];
			foreach ($vars as $var) {
				$var = trim($var);
				$items[] = "[{$var}]";
			}
			
			return implode(', ', $items);
		}
    }

   /**
     * @return array
     */
    public static function getListContentType()
    {
		return [
			self::CONTENT_TYPE_HTML => 'html',
			self::CONTENT_TYPE_TEXT_PLAIN => 'text-plain',
		];
    }	
	
    /**
     * @return string
     */	
	public static function getTitleContentType($content_type)
    {
		$list = self::getListContentType();
		
		if (isset($list[$content_type])) {
			return $list[$content_type];
		} else {
			return '';
		}
    }	
		
	
   /**
     * @return array
     */
    public static function getListSpoolStatus()
    {
		return [
			self::SPOOL_STATUS_AWAIT => 'в ожидании',
			self::SPOOL_STATUS_DELIVERED => 'отправлено',
			self::SPOOL_STATUS_FAILURE => 'ошибка отправки',
		];
    }		

    /**
     * @return string
     */	
	public static function getTitleSpoolStatus($status)
    {
		$list = self::getListSpoolStatus();
		
		if (isset($list[$status])) {
			return $list[$status];
		} else {
			return '';
		}
    }
	
	/**
	 * Получить атрибуты события
	 * @param integer $eventId
	 * @return array
	 */
	public static function getEventAttributes($eventId)
	{
		$result = null;
		if (!empty($eventId)) {
			
			$events = \Yii::$app->params['events'];
			if (isset($events[$eventId])) {
			
				$result = $events[$eventId];
			}
		}
		return $result;
	}	
	
	/**
	 * Сохраняем сообщение по указанному почтовому событию в таблицу писем ACmsMail
	 * @param integer $eventId -- номер почтового события
	 * @param array $data -- данные для экспорта в шаблон письма
	 * @return integer $count -- количество отправленных копий письма
	 */
	public function send($eventId, array $data, $files = [])
	{
		//получаем параметры почтового события (шаблон, настройки и прочее)
		$event = \app\modules\event\models\Event::findOne(['is_active'=>1, 'event_id'=>$eventId]);
		
		$emails = [];
		
		if (!empty($event)) {
			
			//получаем настройки системы для указанного почтового события
			$eventAttributes = self::getEventAttributes($eventId);
			if (!empty($eventAttributes)) {
				
				//удаляем лишние пробелы из строки со списком переменных для письма
				$vars = preg_replace('/\s+?/', '', $eventAttributes['vars']);
				
				//оставляем только те переменные, которые присутствуют в обеих массивах
				$vars = array_intersect(explode(',', $vars), array_keys($data));
				
				//если остались хоть какие-то переменные, внедряем их данные в шаблон письма
				if (is_array($vars) && count($vars)) {
					
					//подготавливаем регекспы
					$regexps = [];
					foreach ($vars as $var) { 
						$regexps[sprintf(self::VAR_TEMPLATE, $var)] = isset($data[$var]) ? $data[$var] : '';
					}
					
					$event->subject = preg_replace(array_keys($regexps), array_values($regexps), $event->subject);
					$event->content = preg_replace(array_keys($regexps), array_values($regexps), $event->content);
				}
			}
			
			if (isset($data['email'])) {
				$emails[] = $data['email'];
			}
			
			if (isset($data['emails']) && is_array($data['emails'])) {
				foreach ($data['emails'] as $e) {
					$emails[] = $e;
				}
			}
			
			$adminIds = Yii::$app->db->createCommand("SELECT admin_id FROM ". \app\modules\admin\models\AdminVsEvent::tableName()." WHERE event_id= {$event->id}")->queryColumn();
			if (!empty($adminIds)) {
				$items = \app\modules\admin\models\Admin::find()
				   ->where(array('id' => $adminIds))
				   ->asArray()				
				   ->all();	

				foreach ($items as $item) {
					$emails[] = $item['email'];
				}
			}
			
			foreach ($emails as $email){
				
				//создаем обьект MailSpool
				$eventSpool = new \app\modules\event\models\EventSpool;
				$eventSpool->post_files = $files;
				$eventSpool->event_id = $event->id;
				
				//устанавливаем тему
				$eventSpool->subject = $event->subject;
				
				//устанавливаем тело сообщения с шаблоном
				$eventSpool->content = $event->content;
					
				$eventSpool->email_to = $email;

				//устанавливаем статус ожидания отправки
				$eventSpool->status = self::SPOOL_STATUS_AWAIT;
				
				//устанавливаем дату создания
				$eventSpool->created_at = time();

				//если стоит мгновенная отправка то отправляем сразу
				if ($event->is_instant) {
					$this->sendToEmail($eventSpool);
				} else {
					$eventSpool->save();
				}
				
			}
		}
		
		return count($emails);
	}	
	
	public function sendToEmail($letter)
	{
		$configValues 	= Settings::getInstance()->getData();
		$events 		= EventModel::getData();
		
		$email 			= Yii::$app->email;
		
		$email->to = $letter->email_to;
				
		$robot 		= !empty($events[$letter->event_id]['from_email']) ? $events[$letter->event_id]['from_email'] : $configValues['event_from_email'];
		$fromName 	= !empty($events[$letter->event_id]['translations'][$letter->lang]['from_name']) ? $events[$letter->event_id]['translations'][$letter->lang]['from_name'] : $configValues['event_from_name'];
				
		$email->from = self::mimeHeaderEncode($fromName).sprintf('<%s>', $robot);
		$email->replyTo = $robot;
		$email->subject = self::mimeHeaderEncode($letter->subject);
		$email->message = preg_replace('/\n/', '<br />', html_entity_decode($letter->content));
		
		return $email->send();
	}
	
	/**
	 * Кодируем заголовок письма
	 * @param string $str
	 * @return string
	 */
	public static function mimeHeaderEncode($str) { return '=?utf-8?b?'.base64_encode($str).'?=';}		
	
}