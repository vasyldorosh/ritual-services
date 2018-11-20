<?php

namespace app\modules\structure\models;

use Yii;
use yii\base\Model;

/**
 * RemindForm is the model behind the login form.
 */
class PageMoveForm extends Model
{
    public $page_id;
    public $parent_id;
   
    public $page;
    public $parent;
   
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // email and password are both required
            [['page_id'], 'required'],
            [['parent_id'], 'required', 'message'=>'Родитель не выбран'],
        ];
    }
	
	public function afterValidate()
	{
		if (!$this->hasErrors()) {
			$this->page = Page::findOne($this->page_id);
			
			if (!empty($this->page)) {
				$this->parent = Page::findOne($this->parent_id);
				
				if (!empty($this->parent)) {
					$pos = strpos($this->page->structure_id, $this->parent->structure_id);
					if (!($pos===0)) {
						//$this->addError('parent_id', "Эта страница не может быть родителем: {$this->page->structure_id} : {$this->parent->structure_id}");
					}
					
				} else {
					$this->addError('parent_id', 'Родитель не найден');
				}
				
			} else {
				$this->addError('page_id', 'Страница не найдена');
			}
		}
		
		return parent::afterValidate();
	}
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'page_id' 	=> 'Страница',
            'parent_id' => 'Родитель',
        ];
    }
	
	public function move() 
	{	
		$this->validate();
		
		if (empty($this->errors)) {
			$structure_id = $this->page->structure_id;
		
			$this->page->structure_id = \app\helpers\Page::buildNewPageStructureId($this->parent_id);	
			
			$validate = $this->page->validate();
			
			if ($validate) {
				Archive::add();
			}
			
			if ($this->page->save()) {
				
				//изменяем structure_id у всех дочерних страниц
				$childs = Page::find()->andWhere("structure_id LIKE  '{$structure_id}%' AND id <> {$this->page->id}")->all();
				foreach ($childs as $child) {
					$child->structure_id = preg_replace('~^(?<!'.$structure_id.')('.$structure_id.')~', $this->page->structure_id, $child->structure_id);
					$child->save();
				}

				return true;
			} else {
				$errors = [];
				foreach ($this->page->errors as $attr=>$data) {
					$errors[] = $data[0];
				}
				$this->addError('page_id', implode("<br/>", $errors));	
			}
		}
		
		return false;
	}	

}
