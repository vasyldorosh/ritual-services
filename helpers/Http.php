<?php
namespace app\helpers;

/**
 * 
 * Помощник Http
 */
class Http
{	
	public static function getStatusByUrl($url)
	{
		$headers = get_headers($url);
		//print_r($headers);
		//die();		
		$status  = substr($headers[0], 9, 3);
		
		return $status;	
	}

}