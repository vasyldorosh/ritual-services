<?php
namespace app\modules\structure\models;

use Yii;
use yii\data\ActiveDataProvider;
use app\components\DumpTableDB;

class Archive extends \app\components\BaseModel
{
    const ARCHIVE_FOLDER = '/files/structure/';
	
	/**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%structure_page_archive}}';
    }
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
           [['id'], 'safe']
        ];
    }	
	
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = [
			'id' => 'ID',
			'created_at' => 'Время создания',
        ];
				
		return $labels;
    }
	
    public function beforeSave($insert) {

        if ($insert) {
            $this->created_at = time();
		} 
        
		return parent::beforeSave($insert);
    }	
	
	public static function add()
	{
		$model = new self;
		$model->save();
		
		return $model->id;
	}	
	
    public function afterSave($insert, $changedAttributes) {
	
		if ($insert) {
			ob_start();
			DumpTableDB::get('structure_page');
			DumpTableDB::get('structure_page_lang');
			DumpTableDB::get('structure_page_block');
			DumpTableDB::get('structure_domain');
			DumpTableDB::get('menu');
			DumpTableDB::get('menu_link');
			DumpTableDB::get('menu_link_lang');
			
			$content = ob_get_clean();
			$file = $this->getFile();
	
			file_put_contents($file, $content);
		}	

        return parent::afterSave($insert, $changedAttributes);
    }	
	
	public static function restore($id)
	{
		$model = self::findOne($id);
		if (!empty($model)) {
			$file = $model->getFile();
			if (is_file($file)) {
				$pdo = Yii::$app->db->pdo;
				$pdo->exec('DELETE FROM `menu_link_lang`; ALTER TABLE  `menu_link_lang` AUTO_INCREMENT =1;');
				$pdo->exec('DELETE FROM `menu_link`; ALTER TABLE  `menu_link` AUTO_INCREMENT =1;');
				$pdo->exec('DELETE FROM `menu`; ALTER TABLE  `menu` AUTO_INCREMENT =1;');
				$pdo->exec('DELETE FROM `structure_page_block`; ALTER TABLE  `structure_page_block` AUTO_INCREMENT =1;');
				$pdo->exec('DELETE FROM `structure_page_lang`; ALTER TABLE  `structure_page_lang` AUTO_INCREMENT =1;');
				$pdo->exec('DELETE FROM `structure_page`; ALTER TABLE  `structure_page` AUTO_INCREMENT =1;');
				$pdo->exec('DELETE FROM `structure_domain`; ALTER TABLE  `structure_domain` AUTO_INCREMENT =1;');
				
				$pdo->exec(file_get_contents($file));
				
				\yii\caching\TagDependency::invalidate(Yii::$app->cache, Page::getTag());
				
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}	
	
	public function getFile()
	{
		return Yii::getAlias('@webroot') . self::ARCHIVE_FOLDER . $this->id . '.sql';
	}	
	
    public function search($params)
    {
        $query = self::find();
        
        $dataProvider = new ActiveDataProvider([
            'query'	=> $query,
            'sort' => [
				'defaultOrder'	=> ['id'=>SORT_DESC],
				'attributes'	=> [],
			],
			'pagination' => false,
	    ]);

        $this->load($params);

        $query->andFilterWhere([
            self::tableName() . '.id' => $this->id,
        ]);
		
	
        return $dataProvider;
    }	
	
    public function afterDelete() {
		@unlink($this->getFile());

        return parent::afterDelete();
    }	
	
	
	
}