<?php
namespace app\helpers;
use Yii;
/**
 * 
 * Помощник Date
 */
class Date
{	

    /* Список месяцев на разных языках */
    public static $months = array(
        'ru' => array (
            1 => 'январь',  2 => 'февраль', 3 => 'март', 4 => 'апрель', 5 => 'май', 6 => 'июнь', 7 => 'июль',
            8 => 'август', 9 => 'сентябрь', 10 => 'октябрь', 11 => 'ноябрь', 12 => 'декабрь',
        ),
        'en' => array (
            1 => 'january', 2 => 'february', 3 => 'march', 4 => 'april', 5 => 'may', 6 => 'june', 7 => 'july',
            8 => 'august', 9 => 'september', 10 => 'october', 11 => 'november', 12 => 'december',
        ),
        'pl' => array (
            1 => 'january', 2 => 'february', 3 => 'march', 4 => 'april', 5 => 'may', 6 => 'june', 7 => 'july',
            8 => 'august', 9 => 'september', 10 => 'october', 11 => 'november', 12 => 'december',
        ),		
    );

    /* Список склоненных месяцев на разных языках */
    public static $bandedMonths = array(
        'ru' => array (
            1 => 'января',  2 => 'февраля', 3 => 'марта', 4 => 'апреля', 5 => 'мая', 6 => 'июня', 7 => 'июля',
            8 => 'августа', 9 => 'сентября', 10 => 'октября', 11 => 'ноября', 12 => 'декабря',
        ),
        'en' => array (
            1 => 'january', 2 => 'february', 3 => 'march', 4 => 'april', 5 => 'may', 6 => 'june', 7 => 'july',
            8 => 'august', 9 => 'september', 10 => 'october', 11 => 'november', 12 => 'december',
        ),
        'pl' => array (
            1 => 'january', 2 => 'february', 3 => 'march', 4 => 'april', 5 => 'may', 6 => 'june', 7 => 'july',
            8 => 'august', 9 => 'september', 10 => 'october', 11 => 'november', 12 => 'december',
        ),		
    );
	
    /* Список склоненных месяцев на разных языках */
    public static $days = [1=>'Понедельник',2=>'Вторник',3=>'Среда',4=>'Четверг',5=>'Пятница',6=>'Субота',0=>'Воскресенье'];
	
	public static function getDayTitle($time)
	{
		$day = date('w', $time);

		return self::$days[$day];
	}
	
	public static function getMonthTitle($time)
	{
		$month = date('n', $time);
		return self::$bandedMonths[l()][$month];
	}
	
	/*
	public static function getDateAsTitle($date, $getTime = true, $getYear = true, $getMounth = true, $getDay = true, $getHowOld = true)
	{
		$now = getdate();
		$date = getdate($date);
		if ($getHowOld && $date['mon'] === $now['mon'] && $date['year'] === $now['year'] && $date['mday'] >= $now['mday']-1) {
			if ($date['mday'] === $now['mday']) {
				$day = t('Сегодня');
			}
			if ($date['mday'] === $now['mday']-1) {
				$day = t('Вчера');
			}
		} else {			
			$day = '';
			$day .= ($getDay  ? $date['mday'] : '');
			$day .= ($getMounth ? $getDay ? ' '.self::$bandedMonths[l()][$date['mon']] : ' '.self::$months[l()][$date['mon']] : '');
			$day .= ($getYear ? ' '.$date['year'] : '');
			$day = trim($day);
		}
		
		if ($getTime) {
			return sprintf('%s в %02d:%02d', $day, $date['hours'], $date['minutes']);			
		} else {
			return $day;
		}
	}
	*/

	public static function getDateAsTitle($time)
	{
		$diff 	 =  time() - $time;
		$minutes =  (int) ($diff / 60);
		$hours 	 =  (int) ($diff / (60*60));
		$days 	 =  (int) ($diff / (60*60*24));
		$years 	 =  (int) ($diff / (60*60*24*365));
		$dateD 		 =  date('j', $time);
		$dateM 		 =  date('n', $time);
		$dateY 		 =  date('Y', $time);
		
		$dateAgo = '';
		
		if ($minutes == 0) {
			$dateAgo = Yii::t('app', '{n, plural, =1{# минуту} one{# минуту} few{# минуты} many{# минут} other{# минут}}', ['n'=>1]);
		
		//1 минута и до 59 минут назад
		} else if ($minutes <= 59) {
			$dateAgo =  Yii::t('app', '{n, plural, =1{# минуту} one{# минуту} few{# минуты} many{# минут} other{# минут}}', ['n'=>$minutes]);
		
		//час назад и минуты и так до 12 часов назад
		} else if ($hours <= 12) {
			$m = $minutes - $hours*60;
			$s = Yii::t('app', '{n, plural, =1{# час} one{# час} few{# часа} many{# часов} other{# часов}}', ['n'=>$hours]);
			if ($m) {
				$s.= ' ' . Yii::t('app', '{n, plural, =1{# минуту} one{# минуту} few{# минуты} many{# минут} other{# минут}}', ['n'=>$m]);
			}
			
			$dateAgo = $s;
		
		//день назад и так до 6 дней
		} else if ($days <= 6) {
			if ($days == 0) {
				$dateAgo =  Yii::t('app', '{n, plural, =1{# день} one{# день} few{# дня} many{# дней} other{# дней}}', ['n'=>1]);
			} else {
				$dateAgo =  Yii::t('app', '{n, plural, =1{# день} one{# день} few{# дня} many{# дней} other{# дней}}', ['n'=>$days]);
			}
			
		//7,14 дней: неделя назад	
		} else if (in_array($days, [7,14, 21])) {
			$weeks = $days / 7;
			
			$dateAgo =  Yii::t('app', '{n, plural, =1{неделя} one{# неделя} few{# недели} many{# недель} other{# недель}}', ['n'=>$weeks]);
			
		//с 15 до 20 дня пишем дни
		} else if ($days <= 30) {
			$dateAgo =  Yii::t('app', '{n, plural, =1{# день} one{# день} few{# дня} many{# дней} other{# дней}}', ['n'=>$days]);
		} else if ($days <= 30) {
			$dateAgo =  Yii::t('app', '{n, plural, =1{# день} one{# день} few{# дня} many{# дней} other{# дней}}', ['n'=>$days]);
		} else if ($days == 30) {
			$dateAgo =  t('месяц');
		} else if ($years == 0) {
			return  $dateD . ' ' .self::$bandedMonths[l()][$dateM];
		} else {
			return  $dateD . ' ' .self::$bandedMonths[l()][$dateM] . ' ' . $dateY . ' ' . t('года');
		}
		
		return $dateAgo . ' ' . t('назад');
	}

}