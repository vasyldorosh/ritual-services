<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

class VstabController extends Controller
{
    public function actionDelete()
    {
        $ids = r()->post('id');
		$fullModelName = r()->get('fullModelName');
	  
        foreach ($ids as $id) {
			$photo = $fullModelName::findOne($id);
            
			if (!empty($photo))
                $photo->delete();
        }
        echo 'OK';
    }

    public function actionCreate($owner_id)
    {
		$modelAttribute = r()->post('modelAttribute');
		$fullModelName 	= r()->post('fullModelName');
		$modelName 		= r()->post('modelName');
        
		$model = new $fullModelName;
        $model->$modelAttribute = $owner_id;
        $model->attributes 		= r()->post($modelName);
		
		$response = [];
		
		if ($model->save()) {
			$response['success'] = 1;
			$response['id']   	 = $model->id;
			$response['rank']    = $model->rank;
			$response['title']   = $model->getTitleList();
			
			if ($model->hasAttribute('image')) {
				$response['preview'] = $model->getImageUrl('image', 134, 100, 'crop');
			}
			
		} else {
			$response['success'] = 0;
			$response['errors']  = $model->errors;
		}
      
        echo json_encode($response);
    }

    public function actionUpdate($id)
    {
		$fullModelName 		= r()->post('fullModelName');
 		$modelName 			= r()->post('modelName');
       
		$m = new $fullModelName;
        $b = $m->behaviors();
        
		$query = $fullModelName::find();
        if (!empty($b['ml'])) {
            $query->multilingual();
        }
        $model = $query->where(['id'=>$id])->one();

		
		$post = r()->post($modelName);

		foreach ($post as $k=>$v) {
			if (substr_count($k, 'image')) {
				if (isset($post[$k]) && !substr_count($v, 'base64')) {
					unset($post[$k]);
				}				
			}
		}		
		$model->attributes = $post;

		$response = [];
		
		if ($model->save()) {
			$response['success'] = 1;
			$response['id']   	 = $model->id;
			$response['rank']    = $model->rank;
			$response['title']   = $model->getTitleList();
			if ($model->hasAttribute('image')) {
				$response['preview'] = $model->getImageUrl('image', 134, 100, 'crop');
			}
			
		} else {
			$response['success'] = 0;
			$response['errors']  = $model->errors;
		}
      
        echo json_encode($response);
    }

    public function actionGet($id)
    {
		$fullModelName 	= r()->get('fullModelName');
        
        $m = new $fullModelName;
        $b = $m->behaviors();
        
		$query = $fullModelName::find();
        if (!empty($b['ml'])) {
            $query->multilingual();
        }
        $model = $query->where(['id'=>$id])->one();
      
		$response = $model->getDataInfo();
      
        echo json_encode($response);
    }

    public function actionOrder()
    {
        $gp = r()->get('order');
       
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
		
		
		$fullModelName = r()->get('fullModelName');

		foreach ($gp as $k => $v) {
            $p = $fullModelName::findOne($k);
            $p->rank = $orders[$i];
            $res[$k] = $orders[$i];
            $p->save();
            $i++;
        }

        echo json_encode($res);
    }

}
