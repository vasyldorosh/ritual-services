<?php
namespace app\modules\menu\widgets;

use yii\base\Widget;

class TreeList extends Widget
{
	/**
	 * @var array the data that can be used to generate the tree view content.
	 *
     * array(
     *      array('text'=>'link1', 'link'=>'http://link.com'),
     *      array('text'=>'simple text, no link'),
     *      array('text'=>'text', 'link'=>'http://link.com',
     *            'childs'=> array(
                    array('text'=>'Text'),
                  )
     *      ),
     *
     * );
	 */
	public $data;

    /**
     * @var array
     */
	public $htmlOptions;

	/**
	 * Initializes the widget.
	 * This method registers all needed client scripts and renders
	 * the tree view content.
	 */
	public function init()
	{
		if(isset($this->htmlOptions['id']))
			$id=$this->htmlOptions['id'];
		else
			$id=$this->htmlOptions['id']=$this->getId();
	}

	/**
	 * Ends running the widget.
	 */
	public function run()
	{
		if (!empty($this->data)) {
		    $html = $this->saveDataAsHtml($this->data);
			echo $html;
		} else {
            die('Не указан массив данных');
        }

	}

    /**
     * Рендерим рекурсивно список
     * 
     * @param array $data
     * @return string
     */
    public function saveDataAsHtml(array $data) {
        // убираем ИД из опций ХТМЛ, что бы ИД был только у корневого списка
        $htmlOptions = $this->htmlOptions;
        $this->htmlOptions = [];
	
        foreach ($data as &$item) {
            if (!empty($item['childs']) && is_array($item['childs'])) {
                $item['childs'] = $this->saveDataAsHtml($item['childs']);
            }
        }
        
        $html = $this->render('treelist', [
        	'listData' => $data,
            'htmlOptions' => $htmlOptions
        ]);
		
		return $html;
    }
}
