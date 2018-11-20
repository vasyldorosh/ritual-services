<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use	app\helpers\Page as PageHelper;
use app\modules\redirect\models\Redirect;
use app\modules\structure\models\Domain;
use app\modules\structure\models\Page;

class SiteController extends Controller
{
	public $structure_id, $title, $head, $description, $h1, $is_canonical, $js_code;	
	
	
    /**
     * id активной страницы
     */
    public $page_id = null;
	
    /**
     * дание для виджетов
     */
    public $activeWidgetData = null;

    /**
     * дание о активном домене
     */
    public $domainData = [];

    /**
     * сообщение об ошибке
     */

    const PAGE_DOES_NOT_FOUND = 'Страница не найдена';

    /**
     * Вектор для переменных
     * @var array
     */
    protected $_variables = [];

    /**
     * Вектор с хлебными крошками
     * @var array
     */
    public $breadcrumbs = [];
   
	protected $_is404Call = false;
    
	protected $_page404 = null;	
	
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }
	
	public function beforeAction($action) {
		$this->enableCsrfValidation = false;
		return parent::beforeAction($action);
	}	

    /**
     * Действие по-умолчанию, для отрисовывания затребованной страницы на фронтенде
     */
    public function actionIndex() {
        //получаем параметры восстребованной страницы
        $page = $this->getPage();
		
        $this->is_canonical = $page['is_canonical'];

        //отрисовываем её
        $html = $this->renderPageContent($page, true);

		if (is_array($html)) {
			echo json_encode($html);
			die();
		}
		
        //чистим код от комментариев и лишних отступов
        $html = preg_replace('~<!--\s+.*\s+-->~', '', $html);
        while (strpos($html, "\t") !== false)
            $html = str_replace("\t", '', $html);
        while (strpos($html, "  ") !== false)
            $html = str_replace("  ", ' ', $html);
        while (strpos($html, "\r\n") !== false)
            $html = str_replace("\r\n", "\n", $html);
        while (strpos($html, "\n \n") !== false)
            $html = str_replace("\n \n", "\n", $html);
        while (strpos($html, "\n\n") !== false)
            $html = str_replace("\n\n", "\n", $html);

        //генерируем заголовок о дате последнего изменения страницы
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
			
        echo $html;
    }

    public function getVariables() {
        return $this->_variables;
    }

    /**
     * Рендерим страницу 404
     * @return bool
     */
    protected function render404Page() {
        header("HTTP/1.1 404 Not Found");
		
		//ставим флаг рендеринга 404ой странице, для избежания бесконечной рекурсии в случае ошибки 404 на 404 странице
        $this->_is404Call = true;

		$page404path = $this->getPage404();
		
        if ($page404path) {

            $page = PageHelper::fetchPage('index/' . $page404path);

            if (!empty($page)) {
				echo $this->renderPageContent($page);
				die();
			}
        }
        return false;
    }

    /**
     * Получаем алиас 404ой страницы сайта из конфига
     * @throws CException
     * @return Page
     */
    protected function getPage404() {
        if (is_null($this->_page404)) {
            $this->_page404 = !empty(Yii::$app->params['page404']) ? Yii::$app->params['page404'] : false;
        }
        return $this->_page404;
    }

    /**
     * Парсим урл
     */
    protected function getPage() {
        $this->resolveRedirect();

        //выясняем доменное имя
        $domain = $this->resolveDomain();


        //получаем ссылку на запрашиваемую страницу
        $uri = trim(Yii::$app->getRequest()->getUrl(), '/');

        //определяем текущий язык
        $uri = $this->resolveLanguage($uri);

        //разбиваем урл на 2 составные части - урл и параметры
        $uriParts = explode('?', $uri);
        if (is_array($uriParts) && count($uriParts)) {
            $uri = trim($uriParts[0], '/');
        }

        if (true) {
            //пытаемся получит страницу по запрошенному адресу
            $pathParts = array('index',);
            if (!empty($uri)) {
                $pathParts[] = $uri;
            }
            
			$implode = implode('/', $pathParts);
            
			$page = PageHelper::fetchPage($implode, $domain);
			
            if (empty($page) || $page['path'] !== implode('/', $pathParts)) {
                //пытаемся распарсить урл правилами, чтобы подставить установленные параметры
                $parameters = $this->aliasesUrlParser($uri, $domain);
            
				if (!empty($parameters)) {
						
                    //запоминаем параметры переменных
                    if (isset($parameters['vars'])) {
                        $this->_variables = $parameters['vars'];
                    }

                    $page = PageHelper::fetchPage('index/' . $parameters['path'], $domain);
                    
					if (empty($page)) {
                        $this->render404Page();
                    }
                } else {
						
                    $this->render404Page();
                }
            } 
        }

        $this->breadcrumbs = PageHelper::getBreadcrumbs($page);
		
        $this->structure_id = $page['structure_id'];
		
		$this->page_id = $page['id'];
		
        return $page;
    }

    /**
     * Определяем язык
     * @param string $url
     * @return string -- урл без языкового параметра
     */
    protected function resolveLanguage($url) {
        //разбиваем урл на элеметы
        $uriParts = explode('/', trim($url, '/'));

        //забираем языковый параметр, который всегда первый и устанавливаем язык пользователя
        if (in_array($uriParts[0], $this->domainData['langs'])) {

            Yii::$app->language = array_shift($uriParts);
        } else {

            Yii::$app->language = $this->domainData['lang'];
        }

        //опять собираем урл
        return trim(implode('/', $uriParts), '/');
    }

    /**
     * Определяем название языка
     * @param string $string
     * @return string
     */
    protected function resolveLanguageName($string) {
        $result = 'ua';
        switch ($string) {
            case 'ua':
            case 'uk':
                $result = 'ua';
                break;
            case 'ru':
                $result = 'ru';
                break;
            default:
                $result = 'en';
                break;
        }
        return $result;
    }

    /**
     * Поиск подходящих параметров в настройках алиасов
     * @param string $url -- запрашиваемый путь
     * @param string $domain -- домен
     * @return array - данные для работы
     */
    protected function aliasesUrlParser($url, $domain) {
        $results = [];
        if (!empty($url)) {

            //просматриваем все правила преобразования-парсинга урлов
            $catch = false;
            if (!empty(\Yii::$app->params['urlAliases'])) {

                foreach (\Yii::$app->params['urlAliases'] as $rule) {

                    //если для правило определен поддомен и поддомен не соотвествует текущему поддомену, то пропускаем совпадение
                    if (isset($rule['attributes']['subDomain']) && !empty($rule['attributes']['subDomain']) && $rule['attributes']['subDomain'] !== $domain) {
                        continue;
                    }

                    //если находим правило для преобразования - то инициализируем переменные и определяем путь к странице
                    if (preg_match($rule['key'], $url, $match)) {

                        $catch = true;

                        //если нет режима "своп", то запоминаем распаршенные данные в переменные
                        if (!isset($rule['attributes']['mode']) || $rule['attributes']['mode'] !== 'swap') {
                            if (isset($rule['attributes']['vars'])) {
                                $results['vars'] = self::initializeVariables($url, $match, $rule['attributes']['vars']);
                            }
                        }

                        //если включен режим своп, то нужно просто подменить урл по указанным правилам
                        if (isset($rule['attributes']['mode']) && $rule['attributes']['mode'] === 'swap') {

                            if (URL_TRANSFORMER_DYNAMIC === $rule['attributes']['type']) {

                                $replacements = [];
                                $index = 1;
                                foreach (explode(',', preg_replace('/\s+/', '', $rule['attributes']['vars'])) as $key) {
                                    $replacements[sprintf('/\{%s\}/', $key)] = $match[$index];
                                    $index++;
                                }

                                $rule['attributes']['path'] = preg_replace(array_keys($replacements), $replacements, $rule['attributes']['path']);
                            }

                            if (URL_TRANSFORMER_STATIC === $rule['attributes']['type']) {

                                $rule['attributes']['path'] = $rule['attributes']['path'];
                            }

                            if (isset($rule['attributes']['vars'])) {
                                $results['vars'] = self::initializeVariables($url, $match, $rule['attributes']['vars']);
                            }
                        }

                        $url = $results['path'] = $rule['attributes']['path'];

                        if (!empty($rule['attributes']['subDomain'])) {
                            $results['subDomain'] = $rule['attributes']['subDomain'];
                        }

                        break;
                    }
                }

            }
        }

        //d($results);
        //возвращаем результат
        return $results;
    }

    /**
     * Инициализируем переменные
     * @param string $url
     * @param string $match
     * @param string $vars
     * @return array
     */
    public static function initializeVariables($url, $match, $vars) {
        $result = [];
        unset($match[0]);

        foreach (explode(',', $vars) as $pos => $varName) {
            $pos++;

            $parts = explode(':', $varName);
            $varName = array_shift($parts);
            $defPos = 0;
            if (count($parts)) {
                $defPos = array_shift($parts);
            }

            if ($defPos) {
                $pos = $defPos;
            }

            $result[trim($varName)] = !empty($match[$pos]) ? urldecode($match[$pos]) : null;
        }
        return $result;
    }

    protected function resolveDomain() {
        $domain = 'localhost';

        //получаем список доменов
        $domainsList = Domain::getAll();
       
        $hostInfo = r()->getHostInfo();
        $hostData = parse_url($hostInfo);
        if (isset($hostData['host']) && !empty($hostData['host'])) {
            $domain = $hostData['host'];
        }

        $domain = str_replace('www.', '', $domain);
        $this->domainData['alias'] = $domain;
        $data = Domain::getAliasId();
        
		
		$domainsData 		= Domain::getAllData();
		$domain_id 			= isset($data[$domain]) ? $data[$domain] : 0;
		
		$this->domainData 	= isset($domainsData[$domain_id]) ? $domainsData[$domain_id] : [];
	
        return $domain;
    }

    /**
     * Заполняем блоки в шаблоне, выводом полученным из виджетов
     * @param string $templateName
     * @param array $data
     * @return string
     */
    private function implementVariables($templateName, $data) {
        ob_start();
        ob_implicit_flush(false);
        require($templateName);
        $content = ob_get_clean();
        foreach (array_keys($data) as $key) {
            $content = str_replace(sprintf(PageHelper::EMBED_TEMPLATE, $key), $data[$key], $content);
        }
        return $content;
    }

    /**
     * Выполняем редиректы
     */
    protected function resolveRedirect() {
        $url = trim(r()->getUrl(), '/');

        $redirectRules = Redirect::getAll();
        $path = null;
        foreach ($redirectRules as $rule => $data) {

            if (preg_match($rule, $url, $matches) && is_array($matches) && count($matches)) {

                $path = isset($data['path']) ? $data['path'] : null;

                //если указаны переменные, нужно их внедрить в строку
                if (isset($data['vars'])) {

                    //уничтожаем исходник строки
                    unset($matches[0]);

                    //получаем переменные
                    $variables = explode(',', $data['vars']);

                    for ($a = 0, $b = count($matches); $a < $b; $a++) {

                        $path = str_replace(sprintf(':%s', $variables[$a]), $matches[$a + 1], $path);
                    }
                }

                if (isset($data['callback'])) {
                    $path = $this->$data['callback']($matches);
                }
            }
        }

        if (!empty($path)) {
            $this->redirect($path, true, 301);
        }
    }

    /**
     * Редирект для просмотра новости
     * @param array $matches
     * @return string
     */
    protected function newsRedirect(array $matches) {
        $path = null;
        if (is_array($matches) && count($matches) == 2) {

            $model = News::model()->findByPk($matches[1]);
            if (!empty($model)) {
                $path = '/' . $model->getUrl();
            }
        }
        return $path;
    }

    /**
     * Отрисовываем затребованную страницу
     * @param array $data
     * @return string
     */
    protected function renderPageContent(array $data, $show = false) {
        $result = null;
		
		//d($data);
		
        if (!empty($data)) {

            //получаем путь к файлу-шаблону страницы
            $templateName = Yii::getAlias('@app/views/templates') . '/' . Yii::$app->params['templates'][$data['template_id']]['alias'] . '.php';
				
            $preparedBlocks = [];

            //подключаем сео
            if (isset($data['langs']) && isset($data['langs'][l()])) {
                $seo = $data['langs'][l()];
                if (is_array($seo) && count($seo)) {
                    foreach (array('title', 'head', 'description', 'h1') as $key) {
                        $this->$key = $seo[$key];
                    }
                }
            }

            try {

                //получаем вектор с заполненными переменными шаблона
                $preparedBlocks = ArrayHelper::merge($preparedBlocks, PageHelper::prepareBlocks($data['blocks']));
				
            } catch (CException $e) {

                if (404 == $e->statusCode && !$this->_is404Call) {
                    $this->render404Page();
                } else {
                    throw $e;
                }
				
				die();
            }

			$mode = r()->get('mode');
            //заполняем шаблон выводом, полученным из виджетов
			if ($mode == 'full-page') {
				
				$result = [ 
					'html' 			=> $this->renderPartial('index', array('content' => $this->implementVariables($templateName, $preparedBlocks),), $show),
					'title' 		=> $this->title,
					'description' 	=> $this->description,
				];
				
			} else {
				$result = $this->render('index', array('content' => $this->implementVariables($templateName, $preparedBlocks),), $show);
			}
		}
        return $result;
	}
	
	public function actionF()
	{
		Yii::$app->cache->flush();
	}
	
	public function actionContact()
	{
		if (r()->isAjax) {
			
			$response = [];
			
			$model = new \app\models\Contact; 
			
			$mode = r()->get('mode');

			if ($mode == 'validate-field') {
				
				$field = r()->get('field');

				if ($model->load(r()->post()) && $model->validate([$field])) {
					$response['success'] = 1;
				} else {
					$response['success'] = 0;
					$response['error']   = $model->errors[$field];
				}
				
			} else if ($model->load(r()->post()) && $model->save()) {
				$response['success']  = 1;
				$response['message'] = '<p style="color:#fff">Успішно відправлено</p>';
				
				$model->sendEmail();
			} else {
				$response['success'] = 0;
				$response['errors']  = $model->errors;
			}
			
			return json_encode($response);			
		}		
		
	}
		
}