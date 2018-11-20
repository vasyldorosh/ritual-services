<?php
namespace app\helpers;

use Yii;
use yii\db\Query;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use app\modules\structure\models\Page as PageModel;
use app\modules\structure\models\Block as PageBlock;
use app\modules\structure\models\Domain;

class Page
{
	public static $_data_storage = [];
	
	/**
	 * Дефолтное состояние дерева страниц сайта
	 */
	const DEFAULT_EXPANDED_STATUS = false;
	
	/**
     * Длинна ID одной страницы в блоке ID
     */
    const ID_PART_LEN = 6;
  
	/**
	 * Разделитель пейджей в пути, в векторе пейджей
	 */
	const PATH_DELIMITER = '/';
		
	/**
	 * Сообщение при невозможности получить параметры страницы
	 */
	const CANNOT_GET_PAGE = 'Невозможно отобразить страницу';
	
	/**
	 * Шаблон для названия ссылки на просмотра страницы
	 */
	const PAGE_TEMPLATE = '%d';
	
	/**
	 * Шаблон блока вставки
	 */
	const EMBED_TEMPLATE = '<!--@%s-->';
	
	/**
	 * Приводим данные о страницах сайта из плоского списка в дерево,
	 * удобное для последующего маппинга при поиске данных востребованной страницы
	 * @param Pages $pages -- вектор со страницами
	 * @param boolean $swap -- переключатель айди/алиас страницы в качестве ключа вектора
	 * @return array
	 */
	public static  function toTree($pages = [], $swap = 1)
	{
		$result = [];
		//идем по списку пейджей, собирая их параметры
		foreach ($pages as $data) {
			
			$blocks = [];
			
			//d($data);
			
			$settings = array(
				'id' 				=> $data['id'],
				'structure_id'		=> $data['structure_id'],
				'template_id' 		=> $data['template_id'],
				'alias' 			=> $data['alias'],
                'title' 			=> isset($data['langs'][l()]['title'])?$data['langs'][l()]['title']:'',
                'domain' 			=> isset($data['domain']) ? $data['domain'] : '',
				'blocks' 			=> $blocks,
			);

			
			if (!empty($data['domain'])) { $settings['domain'] = $data['domain']['alias'];}
            $result = self::_findBranch($result, $data['structure_id'], $settings);
			
		}
       
		$data = $swap ? self::swapIdsToPagesNames($result) : $result;
		
		return $data;
	}
	
	/**
	 * Рекурсивно строим дерево пейджей
	 * @param array $tree - ветка
	 * @param string $id - айдишка обрабатываемого элемента
	 * @param array $data - данные обрабатываемого элемента
	 * @return array - возвращаем обработанную ветку 
	 */
	private static function _findBranch($tree = [], $structureId, $data = []) 
	{
		if (strlen($structureId) == self::ID_PART_LEN) {
			
			$tree[$structureId] = $data;
		} else {
			
			$f = substr($structureId, 0, self::ID_PART_LEN);
			if (!isset($tree[$f])) { $tree[$f] = $data;}
			
			$embeddedPages = [];
			if (isset($tree[$f]['embedded_pages'])) { $embeddedPages = $tree[$f]['embedded_pages'];}
			
			$tree[$f]['embedded_pages'] = self::_findBranch($embeddedPages, substr($structureId, self::ID_PART_LEN), $data);
		}
		return $tree;
	}
	
	/**
	 * Идем по дереву пейджей, переключаемся между ключем-айди на ключ-имя пейджи
	 * для удобства последующего маппинга при поиске данных восстребованной страницы
	 * @param array $tree
	 * @return array
	 */
	private static function swapIdsToPagesNames($tree = []) 
	{
		if (is_array($tree) && count($tree)) {
			foreach ($tree as $id => $data) {
				
				$embeddedPages = [];
				if (isset($data['embedded_pages'])) { $embeddedPages = $data['embedded_pages'];}
				$data['embedded_pages'] = self::swapIdsToPagesNames($embeddedPages);
				
				$prefix = '';
				
				if (strlen($data['structure_id']) === self::ID_PART_LEN) {
					$prefix = !empty($data['domain']) ? $data['domain'].'_' : 'localhost_';
				}
				$tree[$prefix.$data['alias']] = $data;
				unset($tree[$id]);
			}
		}
		return $tree;
	}
	
	/**
	 * Рекурсивно приводим дерево пейджей к удобоваримому виду для класса CTreeView
	 * @param array $treePages
	 * @return array
	 */
	public static function build($currentDomain = '', array $treePages, $activePage = 0)
	{
		$pages = [];
		
		$expanded = self::DEFAULT_EXPANDED_STATUS;
		
		if (count($treePages)) {
			foreach ($treePages as $index => $descr) {
				
				//если ключ данных подходим под параметры, вырезаем название домена
				if (preg_match('/^(.+?)\_index$/', $index, $match)) { $currentDomain = $match[1];}
				
				$descr['domain'] = $currentDomain;
				
			
				
				$title = self::buildTitle($descr, $activePage);
				
				if (is_array($descr) && count($descr) && isset($descr['embedded_pages']) && count($descr['embedded_pages'])) {
				
					$pages[] = array(
						'structure_id'	=> $descr['structure_id'],
						'id'			=> $descr['id'],
						'text'			=> $title,
						'expanded' 		=> $expanded,
						'children' 		=> self::build($currentDomain, $descr['embedded_pages'], $activePage),
					);
				} else {
			
					$pages[] = array('text' => $title,);
				}
				if ($expanded) { $expanded = false;}
			}
		}

		return $pages;
	}
	
	/**
	 * Строим тайтл для записи в структуре CTreeView
	 * @param array $descr
	 * @param string $activePage
	 * @return string
	 */
	protected static function buildTitle($descr = [], $activePage = null)
	{
		$titles = [];
		if (!empty($descr['title'])) { $titles[] = $descr['title'];}
		$titles[] = Html::tag('span', '('.$descr['alias'].')', ['class' => 'gray']);
		
		$attributes = ['/structure/page/view', 'id' => $descr['id']];
		if (!empty($descr['domain'])) { $attributes['domain'] = $descr['domain'];}

		
		$title = Html::a(implode($titles), Url::toRoute($attributes), [
			'name' 		=> isset($descr['id']) ? sprintf(self::PAGE_TEMPLATE, $descr['id']) : '', 
			'style' 	=> (int)r()->get('id')==$descr['id']?'color: red':'',
			'target' 	=> 'visualEditor',
		]);

		return $title;
	}
	
	/**
	 * Получить все страницы сайта в виде вектора, удобоваримого для виджета CTreeView
	 * @return array
	 */
	public static function getAll($currentPageId = '')
	{
		$result = null;
		//получаем все пейджи сайта
		$pages 	= self::getAllPages();
		
		$domains=Domain::getAllData();
		
		//строим домены, если их нет
		if (empty($pages)) { 
			$pages = [];
			foreach ($domains as $domain) {
				$pages[] = self::buildDomainPage($domain);
			}
		} else {
			$issetDomainIds = [];
			foreach ($pages as $page) {
				$issetPageDomainIds[$page['domain_id']] = $page['domain_id'];
			}
			
			foreach ($domains as $domain) {
				if (!in_array($domain['id'], $issetPageDomainIds)) {
					$pages[] = self::buildDomainPage($domain);
				}
			}
		}
	
		if (is_array($pages) && count($pages)) {
			
			$result = self::build(null, self::toTree($pages), $currentPageId);
		}
	
		return $result;
	}
	
	/**
	 * Строим домены
	 * @return array
	 */
	protected static function buildDomainPage($domain)
	{
		$page = new PageModel();
		$page->attributes = array(
			'structure_id' 		=> self::buildNewPageStructureId(), //строим "следующий" не занятый структурный айди
			'template_id'		=> $domain['template_id'],
			'alias'				=> 'index',
			'in_search'			=> 1,
			'title'				=> 'Главная - ' . $domain['alias'],
			'domain_id'			=> $domain['id'],
		);
		$page->save();
			
		$data = $page->attributes;	

		$query = new Query;	
		$langs = $query->select('*')
			->from('structure_page_lang')
			->where(['owner_id'=>$page->id])
			->all();
	
		foreach ($langs as $item) {
			foreach (PageModel::getI18nAttributes() as $attribute) {
				$data['langs'][$item['lang']][$attribute] = $item[$attribute];
			}
		}		
	
		return $data;
	}
	
	/**
	 * Получить список всех переменных шаблона
	 * @param string $templateName
	 * @return array -- вектор со всеми переменными|empty array
	 */
	public static function fetchAllTemplateVariables($templateName)
	{
		$result = [];
		if (file_exists($templateName) && preg_match_all('/<!--@(.+?)-->/', file_get_contents($templateName), $matches)) { $result = $matches[1];}
		return $result;
	}
	
	/**
	 * Обрабатываем блоки, запуская виджеты или внедряя тексты
	 * @param CController $controller -- контроллер, обрабатываемый страницу
	 * @param array $blocks -- вектор с параметрами блоков на странице
	 * @param boolean $wrap -- обрамлять или нет блоки для вставки виджетов и текста
	 * @return array -- вектор со значениями алиас=>вывод
	 */
	public static function prepareBlocks(array $blocks, $wrap = false)
	{
		$content = '';
		$replacements = [];
		if (!empty($blocks)) {
			foreach ($blocks as $block) {
				
				switch ($block->type_id) {
					case 1: //widget
						$data = unserialize($block->content);
						$module = array_shift($data);
						$classNamespace = '\app\modules\\' . $module . '\front\Widget';
						$content = $classNamespace::widget($data);
						break;
					case 2: //text
						$content = $block->content;
						break;
				}
				
				$template = '<div id="block_%s" class="acms_content-block">%s<span class="tip">%s</span><div class="clear"></div></div>';
				
				if ($wrap) {
					$data = ['action' => 'Текстовый блок'];
					
					//если вставлен виджет, то рассерилайзим его параметры
					if ($block->type_id == 1) { $data = unserialize($block->content);}
					$replacement = sprintf($template, $block->alias, $content, sprintf("%s/%s", array_shift($data), isset($data['action']) ? $data['action'] : 'default'));
				} else {
					$replacement = $content;
				}
				$replacements[$block->alias] = $replacement;
			}
		}
		return $replacements;
	}
	
	/**
	 * Оборачиваем контект, контентным блоком для правильного отображения в админке
	 * @param array $block
	 * @return string
	 */
	public function wrapContentBlock($block)
	{
		return Yii::$app->controller->render('application.modules.structure.views.page.content_block', array('alias' => $block['alias'], 'content' => $block['content'],), true);
	}
	
	/**
	 * Формируем айдишку под новую страницу, в записимости от родительского айди
	 * @param integer $parentId -- порядковый номер страницы-родителя
	 * @return string $structureId -- структурный порядковый номер
	 */
	public static function buildNewPageStructureId($parentId = null)
	{
		$model = null;
		$where = '';
		
		//если указан номер родительской страницы, то формируем структурный айди на основе структурного айди родителя
		if (!empty($parentId)) {
			
			$model = self::getPageById($parentId);
			if (!empty($model)) {
			
				//берем максимальную айдишку вложенной страницы
				$where = 'SUBSTRING(`structure_id`, 1, ' . strlen($model['structure_id']) . ') = ' . $model['structure_id'] . ' AND LENGTH(`structure_id`) = ' . strlen($model['structure_id']) . '+'.self::ID_PART_LEN;
			}
			
		} else {
			
			$where = 'LENGTH(`structure_id`) = '.self::ID_PART_LEN;
		}
		
		$id = null;
		
		//пытаемся получить страницу по указанным критериям
		$page = PageModel::find()->select('structure_id')->where($where)->orderBy('structure_id DESC')->one();
		if (!empty($page)) {
			
			//если есть страница, то работаем с её структурным айди
			$id = $page->structure_id;
		} else {
			
			//если нет страницы, то формируем новый
			if (!empty($model)) { 
				$id = sprintf("%s000000", $model['structure_id']);
			} else {
				$id = '000000';
			}
		}
		
		//обрабатываем и приплюсовываем 1
		$structureId = sprintf("%s%06d", substr($id, 0, strlen($id) - self::ID_PART_LEN), 
			(ltrim(substr($id, strlen($id) - self::ID_PART_LEN, self::ID_PART_LEN), 0) + 1));
			
		return $structureId;
	}
	
	public static function getTemplatesList()
	{
		$result = [];
		$templates = Yii::$app->params['templates'];
		if (is_array($templates) && count($templates)) {
			foreach ($templates as $id => $properties) {
				
				$result[$id] = $properties['title'];
			}
		}
		return $result;
	}
	
	public static function getTemplateTitle($template_id)
	{
		$list = self::getTemplatesList();
		
		if (isset($list[$template_id])) {
			return $list[$template_id];
		} else {
			return false;
		}
		
	}
	
	/**
	 * Вернуть параметры затребованной пейджи
	 * @param string $uri -- путь к странице
	 * @return array -- параметры
	 */
	public static function fetchPage($uri, $subDomain = 'localhost')
	{
		$urls = self::buildUrls();
		
		$uriPath = [];
		
		//разбиваем ури на элементы и проверяем каждый на существование в списке пейджей
		foreach (explode('/', ltrim($uri)) as $part) {
		
			//создаем цепочку пейджей
			$uriPath[] = $part;

			//если пейджи с указанной цепочкой не существует - прерываемся
			if (!isset($urls[$subDomain][implode(self::PATH_DELIMITER, $uriPath)])) {
			
				//удаляем последний элемент цепочки
				array_pop($uriPath);
				break;
			}
		}
		
		$urlsPart = [];
		if (!empty($urls[$subDomain]) && isset($urls[$subDomain][implode(self::PATH_DELIMITER, $uriPath)])) {
			
			//получаем параметры страницы по затребованному урлу
			$urlsPart = &$urls[$subDomain][implode(self::PATH_DELIMITER, $uriPath)];
		}
	
	    if(!isset($urlsPart['id'])){
            //если нет страницы по домену пытаемся отдать страницу корневого домена
            $root_domain=Domain::getRootDomain();
            $page=null;
            if($root_domain!=null)
                return self::fetchPage($uri,$root_domain);

        } else {
			//считываем страницу
			$page = self::getPageById($urlsPart['id']);
			///d($page);
        }
			
		if (!empty($page)) {
			$page['template']['title'] = Yii::$app->params['templates'][$page['template_id']]['title'];
			$page['template']['alias'] = Yii::$app->params['templates'][$page['template_id']]['alias'];
			$page['blocks']			   = self::buildBlocks(self::getPageBlock($page['id']));
		} else {
			//генерируем ошибку - страница не найдена
			d('404');
		}
	
		//возвращаем ссылку на пейджу
		$result = array_merge($page, ['path' => implode('/', $uriPath)]);
		
		return $result;
	}
	
	/**
	 * Строим вектор урлов в качестве ключей и сетапов страниц, в качестве значений
	 * @return array -- вектор урлов/настроек страниц
	 */
	public static function buildUrls()
	{
		//вектор с картой урлов сайта
		$urlsMap = [];
		
		$pages = self::getAllPages();
		
		//работаем, если имеются страницы
		if (!empty($pages)) {

            //сортируем по структурному ID		  
            foreach ($pages as $page) {
                $pagesNewSort[$page['structure_id']] = $page;
            }
            $pages = $pagesNewSort;
            ksort ($pages, SORT_STRING);
       
			$depth = 0;
			
			$previousIdLenth = 6;
			
			$uri = [];
			$uriId = [];
			
			$breadcrumbs = [];
			
			$prevDomainAlias = '';
			foreach ($pages as $page) {
				if (!empty($page['domain']['alias'])) {
					$prevDomainAlias = $page['domain']['alias'];
				}
				
				
				if ($previousIdLenth == strlen($page['structure_id'])) {
					
					$depth = $depth > 0 ? $depth-1 : 0;
					
				} else if (strlen($page['structure_id']) < $previousIdLenth) {
					
					for ($a = 0, $b = ($previousIdLenth - strlen($page['structure_id']))/6; $a <= $b; $a ++) { unset($uri[$depth --]);}
				}
				
				//запоминаем ширину структурного айди страницы
				$previousIdLenth = strlen($page['structure_id']);
				
				$uri[$depth ++] = $page['alias'];
				$uriId[$page['alias']] = $page['structure_id'];
		
				$urlsMap[$prevDomainAlias][implode(self::PATH_DELIMITER, $uri)] = array(
					'id' => $page['id'],
					'structure_id' => $page['structure_id'],
				);
			}
		}

		return $urlsMap;
	}

	protected static function getDomainByStructure($structure_id){
        $root_id=substr($structure_id, 0, self::ID_PART_LEN);
        $root= self::getPageByStructureId($root_id);
        if($root!=null){
            return  Domain::getAliasById($root->domain_id);
        } else {
            return "localhost";
        }
    }
	
	/**
	 * Собираем параметры блоков
	 * @param PageBlock $blocks
	 * @return array -- вектор с параметрами блоков
	 */
	protected static function buildBlocks(array $blocks)
	{
		$result = [];
		if (count($blocks)) {
			foreach($blocks as $block) { $result[$block['alias']] = (object)$block;}
		}
		return $result;
	}
	
	/**
	 * Копируем все контентные блоки с источника на указанную страницу
	 * @param integer $sourceId
	 * @param integer $destId
	 */
	public static function copyBlocksContent($sourceId, $destId)
	{
		$result = false;
		if (!empty($sourceId) && !empty($destId)) {
			
			$sourceBlocks = PageBlock::model()->findAllByAttributes(array('page_id' => $sourceId,));
			if (is_array($sourceBlocks) && count($sourceBlocks)) {
				
				foreach ($sourceBlocks as $block) {
					
					$model = new PageBlock;
					$model->attributes = array(
						'alias' 		=> $block->alias,
						'page_id' 		=> $destId,
						'content' 		=> $block->content,
						'type_id'		=> $block->type_id,
					);
					$result = $model->save();
				}
			}
		}
		return $result;
	}

	public static function checkBlock($block_id,$data){
        if((isset($data[$block_id]) && !empty($data[$block_id])) || get_class(Yii::$app->controller) == 'PageController'){
            return true;
        } else {
            return false;
        }
    }
	
	/**
	 * Generates tree view nodes in HTML from the data array.
	 * @param array $data the data for the tree view (see {@link data} for possible data structure).
	 * @return string the generated HTML for the tree view
	 */
	public static function saveDataAsHtml($data)
	{
		$page_id = r()->get('id');
		
		$structure_id = self::getPageAttributeById('structure_id', $page_id);
		$items = str_split($structure_id, 6);
		$pathStructureIds = [];
		$string = '';
		foreach ($items as $item) {
			$string.= $item;
			$pathStructureIds[] = $string;
		}
		
		$html='';
		if(is_array($data))
		{
			foreach($data as $node)
			{
				$node['expanded'] = isset($node['structure_id']) && in_array($node['structure_id'], $pathStructureIds);
				
				if(!isset($node['text']))
					continue;

				if(isset($node['expanded']))
					$css=$node['expanded'] ? 'open' : 'closed';
				else
					$css='';

				if(isset($node['hasChildren']) && $node['hasChildren'])
				{
					if($css!=='')
						$css.=' ';
					$css.='hasChildren';
				}

				$options=isset($node['htmlOptions']) ? $node['htmlOptions'] : [];
				if($css!=='')
				{
					if(isset($options['class']))
						$options['class'].=' '.$css;
					else
						$options['class']=$css;
				}

				if(isset($node['id']))
					$options['id']=$node['id'];
				
				$html.=Html::beginTag('li',$options) . $node['text'];
				if(!empty($node['children']))
				{
					$html.="\n<ul>\n";
					$html.=self::saveDataAsHtml($node['children']);
					$html.="</ul>\n";
				}
				$html.="</li>\n";
			}
		}
		return $html;
	}	

	/**
     * Формируем список виджетов, группируя по их модулям
     * @return array
     */
    public static function getAllWidgets()
    {
    	$widgetsList = [];
    	
    	foreach (Yii::$app->modules as $module => $data) {

    		//создаем namespace виджета
			$namespace = '\app\modules\\'.$module.'\front\Widget';
			
			$root = Yii::getAlias('@app');
			
    		if (is_file($root . '/modules/' . $module . '/front/Widget.php')) {
				$class = new $namespace;
				
				//формируем вектор доступных виджетов
				if ($class::$showInStructure) {				
					$widgetsList[$module] = [
						'title'=>$class->getName(),
						'class'=>$namespace,
					];
				}				
			} 
    	}
						
    	return $widgetsList;
    }	
	
	public static function getBreadcrumbs($page)
	{
		$pages = self::getAllPages();
		
		$breadcrumbs = [];
	        
        $paths 		= explode(self::PATH_DELIMITER, $page['path']);
        $size 		= strlen($page['structure_id']);
        $k = 0;
        for ($i = self::ID_PART_LEN; $i < $size + 1;) {
			$structure_id = substr($page['structure_id'], 0, $i);
            $i = $i + self::ID_PART_LEN;

            $url = '';
            for ($j = 0; $j <= $k; $j++) {
				$url .= '/' . $paths[$j];
            }
            
			$url = str_replace('index', l(), $url);

            $pageBread = self::getPageByStructureId($structure_id);
			if (!$pageBread['is_not_breadcrumbs'] && isset($pageBread['langs'][l()]))
				$breadcrumbs[$url] = $pageBread['langs'][l()]['title'];

			$k++;
        }

        array_shift($breadcrumbs);
        
		return $breadcrumbs;
	}
	
	public static function getAllPages()
	{
		if (!isset(self::$_data_storage['getAllPages'])) {
		
			$domains=Domain::getAllData();
			
			$key	= PageModel::getTag() . '_getAllPages_';
			$data	= cache()->get($key);
			
			if ($data === false) {
				$data = [];
				
				$query = new Query;
				$items = $query->select('*')
					->from('structure_page')
					->orderBy('structure_id')
					->indexBy('id')
					->all();
					
				$query = new Query;	
				$langs = $query->select('*')
					->from('structure_page_lang')
					->all();
		
				foreach ($langs as $item) {
					foreach (PageModel::getI18nAttributes() as $attribute) {
						if (isset($items[$item['owner_id']]))
							$items[$item['owner_id']]['langs'][$item['lang']][$attribute] = $item[$attribute];
					}
				}	
				
				foreach ($items as $i=>$item) {
					$data[$i] = $item;

					if (!empty($item['domain_id'])) {
						$data[$i]['domain'] = isset($domains[$item['domain_id']]) ? $domains[$item['domain_id']] : null;
					}
				}
						
				cache()->set($key, $data, 0, td([PageModel::getTag(), Domain::getTag()]));
			}
			
			self::$_data_storage['getAllPages'] = $data;
		}
		
		return self::$_data_storage['getAllPages'];
	}	
	
	public static function getPageBlock($page_id)
	{
		$page_id= (int) $page_id;
		
		$key	= PageModel::getTag() . '_getPageBlock_' . $page_id;
		
		$data	= cache()->get($key);
		
		if ($data === false) {
			$data = [];
					
			$query = new Query;
			$data = $query->select('*')
				->from('structure_page_block')
				->where(['page_id'=>$page_id])
				->all();
					
			cache()->set($key, $data, 0, td(PageModel::getTag() . '_' . $page_id));
		}
		
		return $data;
	}	
	
	public static function getPageById($id)
	{
		$data = self::getAllPages();
		
		if (isset($data[$id])) {
			return $data[$id];
		} else {
			return null;
		}
	}
	
	public static function getPageByStructureId($structure_id)
	{
		$pages	= self::getAllPages();
		
		$data	= ArrayHelper::getColumn($pages, 'structure_id');
		$index  = array_search($structure_id, $data);
		
		return $pages[$index];
	}

	public static function getPageAttributeById($attribute, $id)
	{
		$pages	= self::getAllPages();
		
		$data	= ArrayHelper::getColumn($pages, $attribute);
		
		return isset($data[$id]) ? $data[$id] : '';
	}
	
	public static function getPagePathById($id)
	{
		$pages	= self::getAllPages();
		$structureAlias	= ArrayHelper::map($pages, 'structure_id', 'alias');		
		
		$structure_id = self::getPageAttributeById('structure_id', $id);
		
		$items = str_split($structure_id, 6);
		
		$string		= '';
		$s_ids 	= [];
		foreach ($items as $item) {
			$string.= $item;
			$s_ids[] = $string;
		}
		
		$paths = [];
		foreach ($s_ids as $s_id) {
			$paths[] = $structureAlias[$s_id];
		}
		
		$link = '';
		unset($paths[0]);
		if (!empty($paths)) {
			$link = '/'.implode('/', $paths) . '/';
		} else {
			$link = '/';
		}
		
		if (count(Yii::$app->params['languages']) > 1) {
			return '/'.l(). $link;
		} else {
			return $link;
		}
		
	}
	
	

}