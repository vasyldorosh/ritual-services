<?php

namespace app\helpers;

use Yii;
use app\helpers\Page as PageHelper;
use yii\helpers\Url;
use app\components\AccessControl;
use app\modules\structure\models\Page as PageModel;

class Tree {

    /**
     * Converts linear array to multidimensional according $parentIdName and $idName items params
     *
     * @static
     * @param array $srcArray
     * @param string $parentIdName
     * @param string $idName
     * @param string $childName
     * @return array
     */
    public static function arrayToTreeArray(array $srcArray, $parentIdName='parent_id', $idName='id', $childName = 'childs') 
    {
        //заполняем темповый вектор пунктами, сгруппированными по айди родителя
        $tempArray = array();
        foreach ($srcArray as &$srcItem) {
            if ( isset($srcItem[$parentIdName], $srcItem[$idName]) ) {
                if (isset($tempArray[$srcItem[$parentIdName]]) && is_array($tempArray[$srcItem[$parentIdName]])) {
                    $tempArray[$srcItem[$parentIdName]][] = &$srcItem;
                } else {
                    $tempArray[$srcItem[$parentIdName]] = array(&$srcItem);
                }
            }
        }
        unset($srcItem);

        //проходимся по исходному списку и формируем родителям чайлды
        foreach ($srcArray as &$src2Item) {
            if  (isset($tempArray[$src2Item[$idName]])) {
                $src2Item[$childName] = $tempArray[$src2Item[$idName]];
            }
        }

        /**
         * Remove from result array everything except items with no parent
         */
        foreach ($srcArray as $key => $value) {
            if (isset($value[$parentIdName]) && $value[$parentIdName] > 0) {
                unset($srcArray[$key]);
            }
        }
        
        return $srcArray;
    }
    
    public static function menu($items) 
    {
		$data = [];
		$data = $items;
	
        
        return $data;
    }
    
    /**
     * Добавляет переменную в указанный массив, переменные типа Var[one][two][three] преобразуются в массив,
     * как и в POST и GET
     *
     * @param string $name
     * @param mixed $value
     * @param array $array link to array
     * @return void
     */
    public static function assignNestedVar($name, $value, &$array)
    {
        $match = [];
        if (preg_match('~^([-a-z0-9_]+)\[(.*)\]~i', $name, $match))
        {
            $name = $match[1];
            $path = explode('][',$match[2]);
            if (!isset($array[$name]) || !is_array($array[$name]))
            {
                $array[$name] = [];
            }
            $elemLink = &$array[$name];
            foreach ($path as $item) {
                if (!isset($elemLink[$item])) {
                    $elemLink[$item] = [];
                }
                if ($item == end($path)) {
                    $elemLink[$item] = $value;
                } else {
                    $elemLink = &$elemLink[$item];
                }
            }
        } else {
            $array[$name] = $value;
        }
    }
	
    /**
     * Построим дерево структуры сайта
     * @return array
     */
    public static function getSiteStructureTree() 
    {
    	//вытягиваем все страницы сайта
        $siteStructure = PageModel::find()->multilingual()->with('blocks')->orderBy('structure_id')->all();
		
        //вектор для хранения промежуточных результатов
        $pages = [];
        
        //строим дерево
        foreach ($siteStructure as $page) {
        	
            $tpage = $page->attributes;
            $tpage['title'] = $page->title;
            
            //собираем тайтлы страниц на всех языках
            $titles =   [Yii::$app->sourceLanguage=>$page->title];
            foreach (array_keys(Yii::$app->params['otherLanguages']) as $language) {
				$titles[$language] = $page->{'title_'.$language};
			}
            $tpage['titles'] = $titles;
            
            $tpage['parent_id'] = 0;
            // что бы все элементы были ссылками
            $tpage['link'] = '';
            // добавим уникальныё ИД для удобвства редактирования
            $tpage['htmlOptions'] = array('id' => $page['id']);
            
            // если страница не коренная
            if (strlen($page['structure_id']) > PageHelper::ID_PART_LEN) {
                // то укажем ид его родителя (для построения деревовидного массива)
                $tpage['parent_id'] = substr($page['structure_id'], 0, (-1)*PageHelper::ID_PART_LEN);
            }
            
            $pages[] = $tpage;
        }
        //преобразовываем в дерево для виджета, выводящего структуру сайта
        $siteStructureTree = self::arrayToTreeArray($pages, 'parent_id', 'structure_id', 'childs');
		
        return $siteStructureTree;
    }	
	
     public static function buildTitlesHash($siteStructure)
    {
    	$results = [];
    	foreach ($siteStructure as $item) {
    		
    		if (isset($item['childs'])) {
    			
    			$results += self::buildTitlesHash($item['childs']);
    		}
    		
    		$results[$item['id']] = $item['titles'];
    		$results[$item['id']]['is_not_menu'] = (int)$item['is_not_menu'];
    	}
    	return $results;
    }	
	
	public static function getSideConfigByModule($module)
	{
		$file =Yii::getAlias('@app') . '/modules/'.$module.'/side.php';
		if (is_file($file))
			return require($file);
		else 
			return [];
	}
	
	public static function sideMenu($items)
	{
		$baseUrl = (\Yii::$app->controller->module->id) . '/' . (\Yii::$app->controller->id) . '/' . (\Yii::$app->controller->action->id);
		
		foreach ($items as $item) {
			$class = $baseUrl==$item['url'] ?'active':'';	
			
			if (AccessControl::can($item['url'])) {
				echo '<li class="'.$class.'">';
				
				$hint = isset($item['hint']) ? $item['hint'] : $item['title'];
				
				
					echo '<a href="/?r='.$item['url'].'" title="'.$hint.'">'.$item['title'].'</a>';
				
				if (isset($item['childs'])) {
					echo '<ul>';
					self::sideMenu($item['childs']);
					echo '</ul>';
				}	
				echo '</li>';
			} 
		}
	}
	
	public static function getModulesRightMenu($all=false)
	{
		$modules = [];
		foreach (Yii::$app->modules as $k=>$v) {
			if (!$all && in_array($k, ['event', 'menu', 'admin'])) {continue;}
			
			$file = Yii::getAlias('@app') . '/modules/'.$k.'/side.php';
			
			if (is_file($file))
				$modules[] = [
					'id' => $k,
					'title' => $k,
				];
		}
			
		usort($modules, "cmpModules");	
			
		return $modules;
	}
	
	
}
