<?php 
namespace app\components;

use Yii;
use app\modules\admin\models\Group;

class AccessControl extends \yii\filters\AccessControl
{	
	public static $access = null;

    /**
     * check access user to action.
     * @return boolean
     */
    public static function can($rule)
    {
		if (is_super_admin()) {
			return true;
		}
		
		$rule = str_replace('/', '.', $rule);
		if ($rule[0] == '.') {
			$rule=substr($rule, 1);
		}		
		
		$list = self::getAccesses();
		$list[] = 'admin.admin.error';
		$list[] = 'admin.admin.access-denied';
		$list[] = 'admin.index.index';
		
		$access = in_array($rule, $list);
		
		if (!$access) {
			//d($rule,0);
			//echo $rule;
		}
		
		return $access;
    }

	public static function getAccesses()
	{
		if (self::$access === null && admin()->isGuest) {
			self::$access = Group::getAccessListByGroup(admin()->identity->group_id);
		}
		
		return self::$access;
	}
	
    public static function getThreeList()
    {
		$skip = array('.', '..');
		$files = scandir(dirname(__FILE__) . '/../modules');
		
		$items = [];
		
		foreach($files as $module_id) {
			$accessFile = dirname(__FILE__) . '/../modules/'.$module_id.'/access.php';	
			if (!in_array($module_id, $skip) && is_file($accessFile)) {	
				$data = include($accessFile);
					
				$items[$module_id]['title'] = $data['title'];
				foreach ($data['items'] as $k=>$v) {
					$items[$module_id]['childs'][$k]['title'] = $v['title'];
					$items[$module_id]['childs'][$k]['actions'] = $v['actions'];
				}					
			}
		}
		
		return $items;
	}
	
}