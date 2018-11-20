<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

class GalleryController extends Controller
{
    /**
     * Removes image with ids specified in post request.
     * On success returns 'OK'
     */
    public function actionDelete()
    {
        $ids = r()->post('id');
		$photoFullModelName = r()->get('photoFullModelName');
	  
        foreach ($ids as $id) {
			$photo = $photoFullModelName::findOne($id);
            
			if (!empty($photo))
                $photo->delete();
        }
        echo 'OK';
    }

    /**
     * Method to handle file upload thought XHR2
     * On success returns JSON object with image info.
     * @param $gallery_id string Gallery Id to upload images
     * @throws CHttpException
     */
    public function actionUpload($gallery_id = null, $name = null)
    {
		$modelAttribute 	= r()->get('modelAttribute');
		$photoFullModelName = r()->get('photoFullModelName');
        
		$model = new $photoFullModelName;
        $model->$modelAttribute = $gallery_id;
        $model->save();

        header("Content-Type: application/json");
        echo json_encode(
            array(
                'id' => $model->id,
                'rank' => $model->rank,
                //'name' => (string)$model->name,
                //'description' => (string)$model->description,
                'preview' => $model->getImageUrl('image', 134, 100, 'crop'),
            ));
    }

    /**
     * Saves images order according to request.
     * Variable $_POST['order'] - new arrange of image ids, to be saved
     * @throws CHttpException
     */
    public function actionOrder()
    {
        $gp = r()->post('order');
       
		$orders = array();
        $i = 0;
        foreach ($gp as $k => $v) {
            if (!$v)
                $gp[$k] = $k;
            $orders[] = $gp[$k];
            $i++;
        }
        sort($orders);
        $i = 0;
        $res = array();
		
		
		$photoFullModelName = r()->get('photoFullModelName');

        foreach ($gp as $k => $v) {
            /** @var $p CatalogProductPhoto */
            $p = $photoFullModelName::findOne($k);
            $p->rank = $orders[$i];
            $res[$k] = $orders[$i];
            $p->save(false);
            $i++;
        }

        echo json_encode($res);
    }

    /**
     * Method to update images name/description via AJAX.
     * On success returns JSON array od objects with new image info.
     * @throws CHttpException
     */
    public function actionUpdate()
    {
        $data = r()->post('photo');
        
		$photoFullModelName = r()->get('photoFullModelName');
      	
        $models = $photoFullModelName::find()->where(['id'=>array_keys($data)])->indexBy('id')->all();
        
		foreach ($data as $id => $attributes) {
			/*
			if (isset($attributes['name']))
                $models[$id]->name = $attributes['name'];
				
			foreach (Yii::app()->params->otherLanguages as $lang=>$title) {
				if (isset($attributes['name_'.$lang]))
					$models[$id]->{"name_$lang"} = $attributes['name_'.$lang];		
			}	
				
            if (isset($attributes['description']))
                $models[$id]->description = $attributes['description'];
				
			foreach (Yii::app()->params->otherLanguages as $lang=>$title) {
				if (isset($attributes['description_'.$lang]))
					$models[$id]->{"description_$lang"} = $attributes['description_'.$lang];		
			}
			*/
			
				
            $models[$id]->save();
        }
        $resp = [];
        foreach ($models as $model) {
		
            $row = array(
                'id' => $model->id,
                'rank' => $model->rank,
                //'name' => (string) $model->name,
                //'description' => (string) $model->description,
                'preview' => $model->getImageUrl('image', 134, 100, 'crop'),
            );
			
			/*
			foreach (Yii::app()->params->otherLanguages as $lang=>$t) {
				$row["name_$lang"] = $model->{"name_$lang"};
				$row["description_$lang"] = $model->{"description_$lang"};
			}
			*/
						
            $resp[] = $row;
        }
        echo json_encode($resp);
    }

}
