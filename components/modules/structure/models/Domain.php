<?php
namespace app\modules\structure\models;

use Yii;
use yii\helpers\ArrayHelper;
use app\helpers\Page as PageHelper;

class Domain extends \app\components\BaseModel
{
	const PHONE_TYPE_CALL 	= 1;
	const PHONE_TYPE_MODAL 	= 2;
	
	public $domain_id;
	
	public $post_langs   = [];
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%structure_domain}}';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['alias', 'template_id', 'lang', 'logo_width', 'logo_height', 'post_langs', 'phone_type'], 'required'],
            [['is_active', 'is_root', 'template_id', 'domain_id', 'logo_width', 'logo_height'], 'integer'],
            [['alias'], 'unique'],		
            [['style', 'google_analytics', 'btn_title', 'phone'], 'safe'],		
        ];
    }
	
	public function afterValidate()
	{
		if (!$this->hasErrors()) {

			if (!in_array($this->lang, $this->post_langs)) {
				$this->addError('lang', 'Языка по умолчанию нет в списке языков');
			}
		}
		
		return parent::afterValidate();
	}
	
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = [
			'id' => 'ID',
			'alias' => 'Домен',
			'is_active' => 'Активность',
			'is_root' => 'Корневой домен',
			'template_id' => 'Шаблон',
			'domain_id' => 'Структура страниц на основание домена',
			'post_langs' => 'Языки',   
			'lang' => 'Язык по умолчанию',   
			'logo' => 'Логотип (header)',
			'logo_hover' => 'Логотип (footer)',
			'logo_width' 	=> 'Ширина логотипа',
			'logo_height' 	=> 'Высота логотипа',
			'style' 	=> 'Style',
			'google_analytics' 	=> 'Google analytics',
			'btn_title' 	=> 'Название телефона в хедере',
			'phone' 		=> 'Телефон в хедере',
			'phone_type' 	=> 'Тип телефона в хедере',
        ];
				
		return $labels;
    }
	
	public static function getPhoneTypeList()
	{
		return [
			self::PHONE_TYPE_CALL 	=> 'Вызов',
			self::PHONE_TYPE_MODAL 	=> 'В модальном окне',
		];
	}
	
	public function getPhoneTypeTitle()
	{
		$list = self::getPhoneTypeList();
		
		if (isset($list[$this->phone_type])) {
			return $list[$this->phone_type];
		} else {
			return false;
		}
	}
	
	public function exportToExcel()
	{
		\moonland\phpexcel\Excel::widget([
			'models' => self::find()->all(),
			'mode' => 'export', //default value as 'export'
			'fileName' => strtolower($this->formName()).'.xls', //default value as 'export'
			'columns' => [
				'id',
				'alias',
				[
					'attribute' => 'template_id',
					'format' => 'text',
					'value' => function($model) {
						return PageHelper::getTemplateTitle($model->template_id);
					},
				],
				'lang',
				'is_active',
				'is_root',				
			],
		]);
	}	
	
	public function exportToCsv()
	{
		$items = self::find()->all();
		$data = array();
		
		$header = array(
			$this->getAttributeLabel('id'),
			$this->getAttributeLabel('alias'),
			$this->getAttributeLabel('template_id'),
			$this->getAttributeLabel('lang'),
			$this->getAttributeLabel('is_active'),
			$this->getAttributeLabel('is_root'),			
		);			
		$data[] = implode(';', $header);
		
		foreach ($items as $item) {											
			$row = array(
				$item->id,
				$item->alias,
				PageHelper::getTemplateTitle($item->template_id),
				$item->lang,
				$item->is_active,
				$item->is_root,				
			);
			
			$data[] = implode(';', $row);
		}		
		
		header('Content-Type: application/csv');
		header('Content-Disposition: attachment; filename="'.strtolower($this->formName()).'.csv"');
	
		$fp = fopen('php://output', 'w');
		foreach ( $data as $line ) {
			$val = explode(";", $line);
			fputcsv($fp, $val);
		}
		fclose($fp);				
	}	
	
	
	public function afterSave($insert, $ca)
	{
		if ($insert && !empty($this->domain_id)) {
			$oldDomain = Domain::findOne($this->domain_id);
			
			$structure_id = \app\helpers\Page::buildNewPageStructureId();
			
			$rootPage  = Page::find()->where(['domain_id'=>$this->domain_id])->andWhere('LENGTH(structure_id) = 6')->one();

			$pages 		= Page::find()->multilingual()->where(['domain_id'=>$this->domain_id])->all();
			//d(count($pages));
			foreach ($pages as $page) {
				$attributes = $page->attributes;
				//d($page->translations);
				foreach ($page->translations as $translation) {
					$translationAttributes = $translation->attributes;
					if (l() == $translationAttributes['lang']) {
						$attributes = array_merge($attributes, $translationAttributes);
					}	
				}		
				unset($attributes['id']);
				
				$attributes['title'] 	= str_replace($oldDomain->alias, $this->alias, $attributes['title']);
				
				$p = new Page;
				$p->attributes 			= $attributes;
				$p->is_validate_alias 	= false;
				$p->domain_id 			= $this->id;
				$p->structure_id		= preg_replace('~^(?<!'.$rootPage->structure_id.')('.$rootPage->structure_id.')~', $structure_id, $page->structure_id);
				
				if ($p->save()) {
					foreach ($page->translations as $translation) {
						$attributes = $translation->attributes;
						if ($attributes['lang'] == l()) {
							continue;
						}
						unset($attributes['id']);
						$l = new PageLang;
						$attributes['title'] 	= str_replace($oldDomain->alias, $this->alias, $attributes['title']);
						$l->attributes = $attributes;
						$l->owner_id = $p->id;
						$l->lang = $attributes['lang'];
						//d($attributes,0);
						$l->save();
					}
					
					foreach ($page->blocks as $block) {
						$attributes = $block->attributes;
						unset($attributes['id']);
						$b = new Block;
						$b->attributes = $attributes;
						$b->page_id = $p->id;
						$b->save();
					}
					
				} else {
					//d($p->errors);
				}
			}
		}
		
		return parent::afterSave($insert, $ca);
	}
	
	public static function getAllData()
	{
		$key 		= self::getTag() . '__getAllData___' . l();
		$data		= Yii::$app->cache->get($key);
		
		if ($data === false) {
			$data = self::find()->asArray()->localized()->indexBy('id')->all();
			
			foreach ($data as $item) {
				$data[$item['id']]['logo'] 			= '/upload/' . $item['logo'];
				$data[$item['id']]['logo_hover'] 	= '/upload/' . $item['logo_hover'];
				$data[$item['id']]['lang'] 			= $item['lang'];
				$data[$item['id']]['langs'] 		= explode(',', $item['langs']);
				$data[$item['id']]['btn_title'] 	= $item['translation']['btn_title'];
				$data[$item['id']]['phone'] 	 	= $item['phone'];
				$data[$item['id']]['phone_type'] 	= $item['phone_type'];
			}
			
			Yii::$app->cache->set($key, $data, 0, td(self::getTag()));	
		}
		
		return $data;		
	}
	
	public function beforeSave($insert)
	{
		if (is_array($this->post_langs) && !empty($this->post_langs))
			$this->langs = implode(',', $this->post_langs);
		
		return parent::beforeSave($insert);
	}	

	public function getPost_langs()
	{
		if (r()->isPost) {
			return $this->post_langs;
		} else {
			if ($this->isNewRecord) {
				return array();
			} else {
				
				return explode(',', $this->langs);
			}
		}
	}	
	
	public static function getAll()
	{
		return ArrayHelper::map(self::getAllData(),'id','alias');	
	}
	
	public static function getAliasId()
	{
		$domains = self::getAll();
		
		return array_flip($domains);
	}
	
	public static function getAliasById($domain_id)
	{
		$list =  self::getAll();
		
		if (isset($list[$domain_id])) {
			return $list[$domain_id];
		} else {
			return false;
		}
	}
	
	public static function getDataById($domain_id)
	{
		$data =  self::getAllData();
		
		if (isset($data[$domain_id])) {
			return $data[$domain_id];
		} else {
			return false;
		}
	}
	
	public static function getRootDomain()
	{
		$items =  self::getAllData();
		foreach ($items as $item) {
			if ($item['is_root']) {
				return $item['alias'];
			}
		}
		
		return null;
	}	
	
	public function getLangTitle()
	{
		$langs = Yii::$app->params['languages'];
		if (isset($langs[$this->lang])) {
			return $langs[$this->lang];
		} else {
			return false;
		}
	}

	public static function find()
    {
	    return new \app\components\multilingual\MultilingualQuery(get_called_class());
    }	
		
	public function behaviors()
	{
		return [
			'image' => [
				'class' => \app\components\behaviors\ImageUploadBehavior::className(),
				'attributeName' => ['logo_hover', 'logo'],
			],
			'ml' => [
				'class' => \app\components\multilingual\MultilingualBehavior::className(),
				'tableName' => "structure_domain_lang",
				'attributes' => ['btn_title'],
			],			
			
		];
	}		
	

}