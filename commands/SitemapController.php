<?php
 
namespace app\commands;
 
use Yii;
use yii\console\Controller;
use app\modules\media\models\Media;
use app\modules\media\models\Category;
use app\modules\structure\models\Domain;
 
/**
 * SitemapController
 */
class SitemapController extends Controller {

	
	public function actionIndex()
	{
		$domains = Domain::getAll();
		
		foreach (Yii::$app->params['languages'] as $lang=>$lang_title) {
			Yii::$app->language = $lang;
			
			$issetMediaIds = [];
			
		
			$doc	= new \DOMDocument("1.0", 'utf-8');
			$urlset = $doc->createElement("urlset");
			$doc->appendChild($urlset);
	
			$xmlns = $doc->createAttribute("xmlns");
			$urlset->appendChild($xmlns);
			$value = $doc->createTextNode('http://www.sitemaps.org/schemas/sitemap/0.9');
			$xmlns->appendChild($value);		

			$xmlnsimage = $doc->createAttribute("xmlns:image");
			$urlset->appendChild($xmlnsimage);
			$value = $doc->createTextNode('http://www.google.com/schemas/sitemap-image/1.1');
			$xmlnsimage->appendChild($value);		
			
			$medias = [];
			foreach ($domains as $domain_id=>$domain_alias) {
				$categories = Category::getItemsByDomain($domain_id);
				
				$categoryIds = [];
				foreach ($categories as $item) {
					$categoryIds[] = $item['id'];
				}
				
				if (!empty($categoryIds)) {
					$mediaIds = db()->createCommand("SELECT media_id FROM module_media_vs_category WHERE category_id IN (".implode(',', $categoryIds).")")->queryColumn();
					
					$ids = [];
					foreach ($mediaIds as $id) {
						if (!in_array($id, $issetMediaIds)) {
							$ids[] = $id;
						}
					}
					
					if (!empty($mediaIds)) {
						$medias = Media::find()->where(['module_media.id'=>$ids, 'is_active'=>1])->orderBy('rank')->all();
						foreach ($medias as $media) {
							$issetMediaIds[] = $media->id;
						}
					}
				}				
			}
			
			if (!empty($medias)) {
				$locValue = 'http://'.$domain_alias;
				
				$url = $doc->createElement("url");
				$urlset->appendChild($url);
					
				$loc = $doc->createElement("loc");
				$url->appendChild($loc);	
				
				$value = $doc->createTextNode($locValue . '/'.l().'/portfolio');
				$loc->appendChild($value);	

				foreach ($medias as $media) {
					$imageImage = $doc->createElement("image:image");
					$url->appendChild($imageImage);
					
					foreach ([
						'image:loc' => $locValue . $media->getImageUrl('image'),
						'image:title' => $media->title,
						'image:caption' => $media->attr_alt,
						'image:geo_location' => $media->address,
					] as $n => $v) {
					
						$node = $doc->createElement($n);
						$imageImage->appendChild($node);

						$value = $doc->createTextNode($v);
						$node->appendChild($value);	
					}
				}
			}
			
			$doc->formatOutput = true;
			$doc->save(dirname(__FILE__) . '/../web/sitemap_image_'.$lang.'.xml');				
		}
	}
	
			
}
 