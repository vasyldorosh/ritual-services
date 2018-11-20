<?php

namespace app\modules\menu\front;

use Yii;
use app\components\BaseWidget;
use app\helpers\Tree;
use app\helpers\Page as PageHelper;
use app\modules\menu\models\Menu;

class Widget extends BaseWidget {

    public $menu_id = null;
    public $template = null;

    /**
     * return string
     */
    public function getName() {
        return 'Меню';
    }

    /**
     * return array
     */
    public function attributes() {
        return parent::attributes() + [
            'menu_id' => 'Меню',
            'template' => 'Шаблон',
        ];
    }

    /**
     * return array
     */
    public function rules() {
        $rules = parent::rules();
        if ($this->action == 'index') {
            $rules[] = [['menu_id', 'template'], 'required'];
        }

        return $rules;
    }

    /**
     * return array
     */
    public static function getActions() {
        return [
            'index' => 'Показать меню',
        ];
    }

    /**
     * return array
     */
    public static function getTemplates() {
        return [
            'header' => 'Header',
        ];
    }

    public function actionIndex() {
        if (empty($this->template)) {
            return;
        }

        //загружаем меню
        $menu = $this->_getMenu();

        if (!empty($menu)) {

            // строим список ссылок с путями и аттрибутом активна/неактивна
            $links = $this->_generateLinksArray();

            //просматриваем меню и определяем активные пункты исходя из текущего урла
            $status = false;
            foreach ($links as $data) {
                //если в текущем меню есть пункт с текущей ссылкой, то довольствуемся автоматически выбранным активным пунктом
                if (Yii::$app->request->url === substr($data['link'], 1)) {
                    $status = true;
                    break;
                }
            }

            //если нет выбранного пункта автоматически, исследуем урл и определяем активные пункты
            if (!$status) {
                foreach ($links as &$data) {
                    if (strpos(Yii::$app->request->url, substr($data['link'], 1)) !== false) {
                        $data['active'] = true;
                        break;
                    }
                }
            }

            // Конвертируем в дерево            
            $links = Tree::arrayToTreeArray($links);

            // Отмечаем родителей активного элемента активными
            $links = $this->_markAllActiveParents($links);

            // шаблоны для меню, сохраненных в бд, лежат в поддиректории content
            echo $this->render($this->template, [
                'links' => $links,
                'menu' => $menu,
            ]);
        }
    }

    /**
     * Загрузить необходимое меню
     * @throws CException
     * @return Menu
     */
    private function _getMenu() {
        return Menu::getItem($this->menu_id);
    }

    /**
     * Загрузить пункты меню
     * @throws CException
     * @return Menu
     */
    private function _getItems() {
        return Menu::getItems($this->menu_id);
    }

    /**
     * Сгенерировать список ссылок
     * @param Menu $menu -- объект меню
     * @param bool $hasActiveFlag -- флаг есть ли у данного меню активный элемент
     * @return array
     */
    private function _generateLinksArray(&$hasActiveFlag = null) {
        $links = [];
        $hasActiveItem = false; // флаг - нашли ли активный пункт
        //для каждой ссылки строим путь href и аттрибут активна или нет

        foreach ($this->_getItems() as $item) {

            // строим путь
            $item['link'] = $this->_buildItemLink($item);

            // определяем активность ссылки
            $item['active'] = $this->_isLinkItemActive($item, $this->menu_id);

            if ($item['active']) {
                $hasActiveItem = true;
            }

            $item['image'] = '';

            $links[] = $item;
        }
        if (!is_null($hasActiveFlag)) {
            $hasActiveFlag = $hasActiveItem;
        }
        return $links;
    }

    /**
     * Построить текстовую ссылку для пункта меню по ИД страницы на которую он ссылается
     * @param array $item
     * @return string
     */
    private function _buildItemLink($item) {

        $link = '';

        // если для страницы не прописана конкретная текстовая ссылка, то строим ей
        if (empty($item['link'])) {
            $link = PageHelper::getPagePathById($item['page_id']);
        } else {

            //если указан абсолютный линк, то оставляем как есть...
            if (preg_match('/^[http|www]/', $item['link'])) {
                $link = $item['link'];
            } else {
                //в противном случае - формируем ссылку
                $link = trim($item['link'], '/');
                if (empty($link)) {
                    $link = sprintf('/%s/', l());
                } else {
                    $link = sprintf('/%s/%s', l(), $link);
                }
            }
        }
        return str_replace('{lang}', l(), $link);
    }

    /**
     * Проверяем активен ли пункт меню основываясь на текущей открытой странице
     * @param array $item
     * @param string $menu_id
     * @return bool
     */
    private function _isLinkItemActive($item, $menu_id = null) {
        $urlToCompare = r()->url;

        $result = strCaseCmp(trim($urlToCompare, '/'), trim($item['link'], '/')) == 0;

        return $result;
    }

    /**
     * Отметить всех родителей активного элемента тоже активными
     * @param array $items
     * @return array
     */
    private function _markAllActiveParents($items) {
        foreach ($items as $key => $item) {
            if (isset($item['childs'])) {
                foreach ($item['childs'] as $k => $child) {
                    if ($child['active']) {
                        $items[$key]['active'] = true;
                    }
                }
            }
        }

        return $items;
    }

}
