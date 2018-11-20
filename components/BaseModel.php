<?php

namespace app\components;

use Yii;
use yii\db\ActiveRecord;

class BaseModel extends ActiveRecord 
{	
	public static function getTag()
	{
		return self::tableName();
	}		

	public static function getTagId($id)
	{
		return Yii::$app->id . self::getTag() . '_' . $id;
	}		

    /**
     * Handle 'afterUpdate' event of the owner.
     */
    public function afterSave($insert, $changedAttributes)
    {
		$this->_clearCache();
		
		return parent::afterSave($insert, $changedAttributes);
    }

    /**
     * Handle 'afterDelete' event of the owner.
     */
    public function afterDelete()
    {
		$this->_clearCache();
    }

	private function _clearCache()
	{
		if (!empty($this->id))
			\yii\caching\TagDependency::invalidate(Yii::$app->cache, self::getTagId($this->id));
		
		\yii\caching\TagDependency::invalidate(Yii::$app->cache, self::getTag());
	}		
	
	public function getTitleData()
	{
		$attr =  in_array('title', $this->behaviors['ml']->attributes) ? 'title' : 'name';
		
		$data = [];
		$data[l()] = $this->$attr;
		foreach (Yii::$app->params['languages'] as $k=>$v) {
			if (!isset($data[$k])) {
				$value = !empty($this->{"{$attr}_{$k}"}) ? $this->{"{$attr}_{$k}"} : $this->$attr;
				$data[$k] = $value;
			}
		}
		
		return $data;
	}	

	public function getHomePageData()
	{
		$key 	= self::getTag() . '_getHomePageData_';
		$data 	= Yii::$app->cache->get($key);
		
		if ($data === false) {
			$data = [];
			$data['всего элементов'] = self::find()->count();
			if ($this->hasAttribute('is_active')) {
				$data['из них активных'] = self::find()->where(['is_active'=>1])->count();			
				$data['не активных'] 	 = self::find()->where(['is_active'=>0])->count();			
			}
			
			Yii::$app->cache->set($key, $data, 0, td([self::getTag()]));
		}
		
		return $data;
	}	

	public function beforeValidate()
    {
		if ($this->hasAttribute('alias') && !in_array($this->formName(), ['Page', 'Domain', 'Block'])) {
 		
			if (empty($this->alias) && !empty($this->title)) { 
				$this->alias = $this->title;
			}
			
			$this->alias = self::urlSafe($this->alias);
			
			$this->checkUnique();
		}
		
		return parent::beforeValidate();
    }
	
	private function checkUnique()
	{
		$where = "alias = '{$this->alias}'";
		if (!empty($this->id)) {
			$where .= " AND id <> {$this->id}";
		}
		
		$model = self::find()->andWhere($where)->one();
		
		if (!empty($model)) {
			$this->alias .= '-1';
			$this->checkUnique();
		}
	}
	
    /**
     * @param string $st Кириллический текст
     * @return string Транслит
     */
    public static function translit($string, $reverse = false)
    {
        $table = array(
            'А' => 'A',
            'Б' => 'B',
            'В' => 'V',
            'Г' => 'G',
            'Д' => 'D',
            'Е' => 'E',
            'Ё' => 'YO',
            'Ж' => 'ZH',
            'З' => 'Z',
            'И' => 'I',
            'Й' => 'J',
            'К' => 'K',
            'Л' => 'L',
            'М' => 'M',
            'Н' => 'N',
            'О' => 'O',
            'П' => 'P',
            'Р' => 'R',
            'С' => 'S',
            'Т' => 'T',
            'У' => 'U',
            'Ф' => 'F',
            'Х' => 'H',
            'Ц' => 'C',
            'Ч' => 'CH',
            'Ш' => 'SH',
            'Щ' => 'CSH',
            'Ь' => '',
            'Ы' => 'Y',
            'Ъ' => '',
            'Э' => 'E',
            'Ю' => 'YU',
            'Я' => 'YA',

            'а' => 'a',
            'б' => 'b',
            'в' => 'v',
            'г' => 'g',
            'д' => 'd',
            'е' => 'e',
            'ё' => 'yo',
            'ж' => 'zh',
            'з' => 'z',
            'и' => 'i',
            'й' => 'j',
            'к' => 'k',
            'л' => 'l',
            'м' => 'm',
            'н' => 'n',
            'о' => 'o',
            'п' => 'p',
            'р' => 'r',
            'с' => 's',
            'т' => 't',
            'у' => 'u',
            'ф' => 'f',
            'х' => 'h',
            'ц' => 'c',
            'ч' => 'ch',
            'ш' => 'sh',
            'щ' => 'csh',
            'ь' => '',
            'ы' => 'y',
            'ъ' => '',
            'э' => 'e',
            'ю' => 'yu',
            'я' => 'ya',
            
            'ї' => 'yi',
            'і' => 'i',
            'є' => 'ye',
        );

        $output = str_replace(
            array_keys($table),
            array_values($table),$string
        );

        return $output;
    }

    /**
     * Преобразуем текст в ссылко-безопасный (можно использовать в ссылках)
     * @static
     * @param string $string
     * @param string $c символ для замены недопустимых для ссылки знаков
     * @return mixed
     */
    public static function urlSafe($string, $c = '-')
    {
        $string = preg_replace('/[^-a-z0-9_]+/i', $c, strtolower(self::translit($string)));
        $string = preg_replace('/^-/i', '', $string);
        $string = preg_replace('/-$/i', '', $string);
        return $string;
    }

	
}