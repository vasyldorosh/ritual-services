<?php 

function yii()
{
	return Yii::$app;
}

	function price_format($value)
	{	
        $value = (int) $value;
        
        return number_format(ceil($value), 0, ',', ' ') . ' грн.';
	}


/**
 * A function that will elemenate the long lines of coding to get the parameter.
 * Yii::$app->params['Parameter']
 *
 * If there will be a nested parameter we defined, we can pull the data by calling this function, and
 * used the character "." (period), example below
 *
 * 1. param('data')
 * 2. param('data.mysample');
 *
 * @param string $attribute to pull
 * @return mixed value of params
 */
function param($attribute, $default = null)
{
	$s = explode(".", $attribute);
	$param = yii()->params;
	while (count($s))
		$param = $param[array_shift($s)];
	if ($param === null) {
		$param = $default;
	}

	return $param;
}

function l() {
	return Yii::$app->language;
}

function db() {
	return Yii::$app->db;
}

function cache() {
	return Yii::$app->cache;
}

function r() {
	return Yii::$app->request;
}

function t($message) {
	return Yii::t('app', $message);
}

function td($tags) {
	return new \yii\caching\TagDependency(['tags'=>$tags]);
}

function d($var, $exit = true)
{
    $dumper = new yii\helpers\BaseVarDumper();
    echo $dumper::dump($var, 10, true);
    if ($exit)
        exit;
}

function cmpModules ($a, $b) {
	$aVal = (int)\app\modules\settings\models\Settings::getInstance()->getValue('module_rank_'.$a['id'], 0);
	$bVal = (int)\app\modules\settings\models\Settings::getInstance()->getValue('module_rank_'.$b['id'], 0);
	
	if ($aVal == $bVal) return 0;
	
	return ($aVal > $bVal) ? -1 : 1;
}

function cmpArrayTime ($a, $b) {
	if ($a['publish_time'] == $b['publish_time']) return 0;
		return ($a['publish_time'] > $b['publish_time']) ? -1 : 1;
}

function cmpArrayRank ($a, $b) {
	if ($a['rank'] == $b['rank']) return 0;
		return ($a['rank'] > $b['rank']) ? 1 : -1;
}

function cmpArrayCreateTime ($a, $b) {
	if ($a['create_time'] == $b['create_time']) return 0;
		return ($a['create_time'] > $b['create_time']) ? -1 : 1;
}

function cmpArrayRating ($a, $b) {
	if ($a['rating'] == $b['rating']) return 0;
		return ($a['rating'] > $b['rating']) ? -1 : 1;
}

function admin() {
	return Yii::$app->user;
}

function switchUrl($url, $lang=FALSE)
{
	//по-умолчанию возвращаем текущий урл
	$result = $url;
        
    if($lang===FALSE) // по умолчанию используем текущий язык сайта
		$lang = l();
                      
	if (!empty($url) && !empty($lang)) {
			
		//разбиваем урл на составные части
		$uriParts = explode('/', trim($url, '/'));
		if (is_array($uriParts) && count($uriParts)) {
				
			//берем первый элемент урла и проверяем его на наличие языкового параметра
			$firstItem = $uriParts[0];
			if (in_array($firstItem, array_keys(param('languages')))) {
				//если он действительно языковый параметр - уничтожаем его
				unset($uriParts[0]);
			}
		}
		
		//если указанный язык не входит в список допустимых, то используем текущий язык
		if (!in_array($lang, array_keys(param('languages')))) { $lang = l();}
			
		//формируем ссылку с новым языковым параметром
		$buildUrl = implode('/', $uriParts);
		$result = sprintf('/%s/%s', $lang, $buildUrl);
		if (!empty($buildUrl) && strpos($result, '?')===false) { // если нет get'а, добавляем слеш в конец ссылки
			$result .= '/';
        }
			
	}
	
	
	return $result;
}

function session() {
	return Yii::$app->getSession();
}

function breadcrumbs($items) {
	$html = '<li><a href="/'.l().'/">'.t('Главная').'</a></li>';
	foreach ($items as $link=>$title) {
		if ($link != '#') {
			$html .= '<li><a href="'.$link.'">'.$title.'</a></li>';
		} else {
			$html .= '<li class="active">'.$title.'</li>';
		}
	}
	
	return $html;
}

function img($image, $width, $height, $mode) {
	if (!empty($image)) {

		return \app\components\behaviors\ImageUploadBehavior::thumb($image, $width, $height, $mode);
		
	} else {
		return null;
	}
}

function avatar_letter($string, $class='') {
	$string = trim($string);
	$letter = substr(strtolower($string), 0, 1);
	
	$colors = param('colors');
	$data = ['red', 'white'];
	if (isset($colors[$letter])) {
		$data = $colors[$letter];
	}
	
	return '<div class="'.$class.'" style="background-color: '.$data[0].'"><div style="color: '.$data[1].'">'.strtoupper($letter).'</div></div>';
}

function user_img($user, $size) {
	if (!empty($user['image'])) {
		return '<div class="ava-'.$size.'"><a href="/'.l().'/profile/'.$user['id'].'/"><img src="'.$user['image'].'"></a></div>';
	} else {
		$string = trim($user['name']);
		$letter = substr(strtolower($string), 0, 1);
		
		$colors = param('colors');
		$data = ['red', 'white'];
		if (isset($colors[$letter])) {
			$data = $colors[$letter];
		}
		
		return '<div class="ava-'.$size.'" style="background-color: '.$data[0].';color: '.$data[1].'"><a href="/'.l().'/profile/'.$user['id'].'/">'.strtoupper($letter).'</a></div>';		
	}
}

function xss($string) {
	return trim(htmlentities(strip_tags($string), ENT_QUOTES, 'UTF-8'));
}

function is_ip_ban($ip='') {
	return \app\modules\comment\models\IpBan::isBan($ip);
}

function is_super_admin() {
	if (admin()->isGuest) {
		return 0;
	} else {
		return admin()->identity->group->is_super;
	}
}

function is_ban() {
	if (is_ip_ban()) {
		return true;
	} else if (!user()->isGuest) {
		return user()->identity->is_ban_comment;
	}
	
	return false;
}

    /**
     * Обрезка UTF строки
     * 
     * @static
     * @param string $string
     * @param int $length
     * @param string $postfix
     * @return bool|string
     */
    function truncate($string, $length = 50, $breakWords = false, $postfix = '...')
    {
        $wordEndChars = ',.?!;:'; /* символы окончания слова */
        $truncated = trim($string);
        $length = (int)$length;
        if (!$string) {
            return $truncated;
        }
        $fullLength = iconv_strlen($truncated, 'UTF-8');
        if ($fullLength > $length) {
            $truncated = trim(iconv_substr($truncated, 0, $length, 'UTF-8'));
            if (!$breakWords) {
                $words = explode(' ', $truncated);
                $wordCount = sizeof($words);
                if (rtrim($words[$wordCount-1], $wordEndChars) == $words[$wordCount-1]) {
                    unset($words[$wordCount-1]);
                }
                $wordCount = sizeof($words);
                if (!empty($words[$wordCount-1])) {
                	$words[$wordCount-1] = rtrim($words[$wordCount-1], $wordEndChars);	
                }
                $truncated = implode(' ', $words);
            }
            $truncated .= $postfix;
        }
        return $truncated;
    }
	
	function conf($key) {
		return app\modules\settings\models\Settings::getInstance()->getValue($key);	
	}	
	
	
	function np_replace($str)
	{
		$str = str_replace(array('&nbsp;', ';', "\n", "\r", "\t"), array(' ', ',', ' ', ' ', ' '), $str);
		
		return trim($str);
	}	
	
function d_l($path, $p_lang='')
{
	$l = ($p_lang!='') ? $p_lang : l();
    
    $lang = \Yii::$app->sourceLanguage;
	if (isset(Yii::$app->controller->domainData)) {
		$domain = Yii::$app->controller->domainData;
		if (isset($domain['lang']))
			$lang = $domain['lang'];
	}
	
    if ($lang) {
		$path = str_replace('/' . $lang . '/', '/', $path);
		if ($path == '/'.$lang) {
			$path = '/';
		}
	} 
    
    if (substr($path, -1) != '/') {
        $path .= '/';  
    }
    
    if ($lang != $l && substr($path, 1, 2) != $l) {
        $path = '/' . $l . $path;
    }
    
    if ($lang == $l) {
		$path = str_replace('/' . $l . '/', '/', $path);
    }
    
    return $path;
}
