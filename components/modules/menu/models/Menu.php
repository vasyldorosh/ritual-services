<?php
namespace app\modules\menu\models;

use Yii;
use yii\db\Query;

class Menu extends  \app\components\BaseModel
{
	public $menuTree;
	
	private $_saved_ids	= [];
	private $_link_ids	= [];
	
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'menu';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'type'], 'required'],
            [['is_active'], 'integer'],
            [['menuTree'], 'safe'],
        ];
    }
	
	public function beforeValidate()
	{
		if (empty($this->is_active)) {
			$this->is_active = 0;
		}
		
		return parent::beforeValidate();
	}
	
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = [
            'id' 		=> 'ID',
            'is_active' => 'Активность',
            'type' 		=> 'Тип',
            'title' 	=> 'Название',
        ];
		
		return $labels;
    }
		
	public static function getList()
	{
		 return \yii\helpers\ArrayHelper::map(self::find()->all(), 'id', 'title');
	}
	
	public static function getTypes()
	{
		return [
			'NESTED'=>'Вложенное', 
			'NORMAL'=>'Обычное',
		];
	}
	
	public function getTypeTitle()
	{
		$list = self::getTypes();
		if (isset($list[$this->type])) {
			return $list[$this->type];
		} else {
			return false;
		}		
	}
	
	public function getLinks()
	{
		return $this->hasMany(MenuLink::className(), ['menu_id' => 'id'], ['order'=>'rank']);
	}
	
	public function getLinksMultilingual()
	{
		return MenuLink::find()->multilingual()->where('menu_id=:menu_id', [':menu_id'=>(int)$this->id])->orderBy('rank')->all();
	}

	public function getLinksTree()
	{
		if (empty($this->id)) {
			return [];
		} else {
			$linksTree = [];
			
			$links = $this->getLinksMultilingual();
			
			if (!empty($links)) {
				$linksArray = [];
				foreach ($links as $link) { 
					$properties = $link->attributes;

					$properties['file'] = '';              	
					
					if (!empty($properties['image'])) {
						$properties['image'] = '/upload/' . $properties['image'];
					}
					
					foreach (array_keys(Yii::$app->params['otherLanguages']) as $language) 
					{ 
						$properties['title_'.$language] = $link->{'title_' . $language};
						$properties['description_'.$language] = $link->{'description_' . $language};
					}
					$properties['title'] = $link->title;
					$properties['description'] = $link->description;
					$linksArray[] = $properties;
					
				}
				$linksTree = \app\helpers\Tree::arrayToTreeArray($linksArray, 'parent_id', 'id', 'childs');
			}

			return $linksTree;
		}
	}

    /**
     * Сохраняем все элементы текущего меню
     * @param array $elements -- массив элементов
     * @param void $parentId 
     * @return void
     */
    protected function saveMenuElements(array $elements, $parentId = null) {
        $i = 1; // счетчик позиции пункта меню
		
        foreach ($elements as $element) {
			
			if (empty($element['image']) || strpos($element['image'], 'upload')) {
				unset($element['image']);
			}
			
            // формируем текущий элемент меню
            $element['parent_id'] = ($this->type == 'NESTED' ? $parentId : null);
			
            $element['menu_id'] = $this->id;
            $element['rank'] = $i;
			
			$link = null;
			if (isset($element['id'])) {
				$link = MenuLink::find()->multilingual()->where('id=:id', [':id'=>$element['id']])->one();
				if (!empty($link)) {
					$this->_saved_ids[] = $link->id;
				} 
			}
			if (empty($link)) {
				$link = new MenuLink;
			}
			
            // сохраняем текущий элемент меню
            
			foreach ($element as $k=>$v) {
				if (empty($v)) {
					$element[$k] = null;
				}
			}
			
            $link->attributes = $element;
			
            foreach (array_keys(Yii::$app->params['otherLanguages']) as $language) {
                $var = 'title_' . $language;
                $link->$var = $element[$var];
                $var = 'description_' . $language;
				
				if (isset($element[$var]))
					$link->$var = $element[$var];
            }
            $link->save();

            // если у элемента меню есть чайлды то проходимся и по ним рекурсией
            if (!empty($element['childs']) && is_array($element['childs'])) {
                $this->saveMenuElements($element['childs'], $link->id);
            }
            $i++;
        }
    }	
	
    public function afterFind() {
		$links = $this->links;
			
		foreach ($links as $link) {
			$this->_link_ids[] = $link->id;
		}
		
		return parent::afterFind();
    }	
	
	public function afterSave($insert, $changedAttributes)
	{
		if (!empty($this->menuTree)) {
			$this->saveMenuElements(json_decode($this->menuTree, 1), 0);
			
			//удалим все старые пункты этого меню
			foreach ($this->_link_ids as $isset_id) {
				if (!in_array($isset_id, $this->_saved_ids)) {
					$deletedLink = MenuLink::findOne($isset_id);
					if (!empty($deletedLink))
						$deletedLink->delete();
				}
			}			
		}
		
		return parent::afterSave($insert, $changedAttributes);
	}
	
	public function afterDelete()
	{
		$links = $this->links;
		
		foreach ($links as $link) {
			$link->delete();
		}
		
		return parent::afterDelete();
	}
	
    public static function getItem($id) {
        $id = (int) $id;
            
		$key	= self::getTag() . '_getItem_' . $id . l();
        $data	= cache()->get($key);
			
        if ($data === false) {
			$data = [];
			
			$query = new Query;
			$item = $query->from(self::tableName())->where(['is_active'=>1, 'id'=>$id])->one();
			if (!empty($item)) 
				$data = $item;
			
			cache()->set($key, $data, 0, td(self::getTag()));
		}
			
		return $data;
    }
	
   public static function getItems($id) {
        $id = (int) $id;
            
		$key	= self::getTag() . '_getItems_' . $id . l();
        $data	= cache()->get($key);
			
        if ($data === false) {
			$data = [];
				
			$links = MenuLink::find()->where(['menu_id'=>(int)$id, 'is_active'=>1])->joinWith(['translation'])->orderBy('rank')->all();
				
			if (!empty($links)) {
					
				foreach ($links as $link) { 
					$properties = $link->attributes;

					$properties['title'] = $link->title;
					$properties['description'] = $link->description;
					$data[] = $properties;
						
				}
			}

			cache()->set($key, $data, 0, td([self::getTag(), MenuLink::getTag()]));
		}
			
		return $data;
    }
	
}