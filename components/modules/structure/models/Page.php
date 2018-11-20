<?php
namespace app\modules\structure\models;

use Yii;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;
use app\helpers\Page as PageHelper;
use app\components\traits\Tag;

class Page extends \app\components\BaseModel
{
	public $is_validate_alias = true;
	
	public $parent_id;

	/**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'structure_page';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'alias', 'template_id'], 'required'],
            [['structure_id', 'head', 'description', 'h1'], 'safe'],
            [['alias'], 'validateAlias'],
			[['is_canonical', 'template_id', 'is_search', 'parent_id', 'is_not_breadcrumbs', 'is_not_menu', 'domain_id'], 'number', 'integerOnly' => true,]			
        ];
    }

	/**
	 * Проверяем, чтобы алиас страницы был уникальным на одном уровне (кроме корневого, здесь можно)
	 */	
	public function validateAlias()
	{
		if (!$this->hasErrors() && $this->is_validate_alias) {
			
			//если ширина структурного кода больше базового
			if (strlen($this->structure_id) > PageHelper::ID_PART_LEN) {
				
				$where = 'SUBSTR(structure_id, 1, :parentLength) = :parentId AND LENGTH(structure_id) = :length';
				$parentLength = strlen($this->structure_id)-PageHelper::ID_PART_LEN;
				$params = [
					':parentLength' => $parentLength,
					':parentId' => substr($this->structure_id, 0, $parentLength),
					':length' => strlen($this->structure_id),
				];
				
				//если страница существуют, то исключаем сравнение себя с собой
				if (!empty($this->id)) { 
					$where.= ' AND id <> :selfId';
					$params[':selfId'] = $this->id;
				}	
				
				$pages = static::find()->where($where)->addParams($params)->all();
				
				if (is_array($pages) && count($pages)) {
					foreach ($pages as $page) { 
						if ($this->alias === $page->alias) { 
							$this->addError('alias', 'Название страницы должно быть уникальным в пределах вузла'); 
							break;
						}
					}
				}
			}
		}
	}
	
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = [
			'id' 				=> 'ID',
			'structure_id' 		=> 'Структурный айди',
			'template_id' 		=> 'Шаблон',
			'alias' 			=> 'Название страницы',
			'title' 			=> 'Заголовок',
			'head' 				=> 'Теги в HEAD',
			'h1' 				=> 'H1',
			'description' 		=> 'Описание',
			'is_search'			=> 'Участвовать в поиске',
			'is_canonical'		=> 'Признак страницы canonical',
			'domain'			=> 'Домен',
			'is_not_breadcrumbs'=> 'Не выводить в breadcrumbs',
			'is_not_menu'		=> 'Запрещена для показа в меню',
        ];
		
		foreach ($this->behaviors['ml']->attributes as $attr) {
			foreach (Yii::$app->params['otherLanguages'] as $lang=>$langTitle) {
				$langAttr = $attr . '_' . $lang;
				
				if (isset($labels[$attr])) {
					$labels[$langAttr] = $labels[$attr];
				} 
			}
		}
		
		return $labels;
    }
	
	public static function find()
    {
	    return new \app\components\multilingual\MultilingualQuery(get_called_class());
    }	
	
	public function behaviors()
	{
		return [
			'ml' => [
				'class' => \app\components\multilingual\MultilingualBehavior::className(),
				'tableName' => "{{%structure_page_lang}}",
				'attributes' => self::getI18nAttributes(),
			],			
		];
	}
	
	public static function getI18nAttributes()
	{
		return [
			'title', 'head', 'description', 'h1',
		];
	}
	
	public function getDomain()
	{
		return $this->hasOne(Domain::className(), ['id' => 'domain_id']);
	}

	public function getBlocks()
	{
		return $this->hasMany(Block::className(), ['page_id' => 'id']);
	}

	/**
	 * Выполняем ряд обязательных действий после сохранения модели
	 * @return boolean -- результат выполнения операции
	 */	
	public function afterSave($insert, $changedAttributes)
	{
		
		return parent::afterSave($insert, $changedAttributes);
	}
	
	/**
	 * Выполняем ряд обязательных действий перед сохранения модели
	 * @return boolean -- результат выполнения операции
	 */	
	public function beforeSave($insert)
	{
		if (empty($this->domain_id) && !empty($this->parent_id)) {
			$parent = self::findOne($this->parent_id);
			$this->domain_id = $parent->domain_id;
		}
		
		return parent::beforeSave($insert);
	}
	
	/**
	 * Выполняем ряд действий после удаления
	 * @return boolean -- результат выполнения операции
	 */
	public function afterDelete()
	{		
		$items = Page::find()->where('structure_id LIKE :structure_id')->addParams([':structure_id'=>$this->structure_id.'%'])->all();
		foreach ($items as $item) {
			$item->delete();
		}		
			
		return parent::afterDelete();
	}		
	
	public static function getList()
	{
		$items = self::find()->multilingual()->orderBy('structure_id')->where('is_not_menu=0')->all();
		$pages = [];
		foreach ($items as $item) {
			$count = strlen($item->structure_id) / PageHelper::ID_PART_LEN - 1;
			$lines = '';
			for ($i=1;$i<=$count;$i++) {
				$lines.= '-';
			}
				
			$pages[$item->id] = "{$lines} " . $item->title;
		}

		return $pages;
	}
	
	public static function getListMove($page)
	{
		$items = self::find()->with(['translation'])->orderBy('structure_id')->where(['domain_id'=>$page->domain_id])->all();
		$pages = [];
		foreach ($items as $item) {
			$count = strlen($item->structure_id) / PageHelper::ID_PART_LEN - 1;
			$lines = '';
			for ($i=1;$i<=$count;$i++) {
				$lines.= '-';
			}
				
			$pages[] = [
				'title' 		=> "{$lines} " . $item->title,
				'id' 			=> $item->id,
				'alias' 		=> $item->alias,
				'structure_id' 	=> $item->structure_id,
			];
		}

		return $pages;
	}
	
	
}