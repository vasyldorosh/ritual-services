<?php
namespace app\helpers;

/**
 * 
 * Помощник видео Video
 */
class Video
{	
	public static function getImageByUrl($link)
	{
		$status  = @\app\helpers\Http::getStatusByUrl($link);	
		
		if (!$status || $status != 200) {
			return '';
		}
		
		if (stripos($link, 'youtube')) {
			$id = self::getYoutubeIdByUrl($link);
			if (empty($id)) return '';
			
			return self::getYoutubeImageById($id);
		} else if (stripos($link, 'vimeo')) {
			$id = self::getVimeoIdByUrl($link);
			if (empty($id)) return '';
			
			return self::getVimeoImageById($id);
		} else if (stripos($link, 'coub')) {
			$id = self::getCoubIdByUrl($link);
			if (empty($id)) return '';
			
			return self::getCoubImageById($id);
		}
	}

	public static function getYoutubeImageById($id)
	{
		if ($id == false) return '';
		
		$src = 'http://img.youtube.com/vi/'.$id.'/0.jpg';
		
		return $src;
	}
	
	public static function getYoutubeIdByUrl($url)
	{
		$url = str_replace('https', 'http', $url);
		
		if(strpos($url, 'youtu') === false) return $url;
		
		preg_match('/(http):\/\/youtu.be\/(.*)/', $url, $res);
		if(!isset($res[1]))
			preg_match('/http:\/\/www.youtube.com\/watch\?v=([^&]*)/', $url, $res);
		if(!isset($res[1]))
			preg_match('/http:\/\/www.youtube.com\/watch\?feature=player_detailpage&v=([^&]*)/', $url, $res);
		if(!isset($res[1]))
			preg_match('/http:\/\/www.youtube.com\/embed\/([^?]*)\?feature=player_profilepage/', $url, $res);
		if(!isset($res[1]))
			preg_match('/http:\/\/www.youtube.com\/v\/([^?]*)/', $url, $res);
		if(!isset($res[1]))
			preg_match('/http:\/\/www.youtube.com\/embed\/([^?]*)/', $url, $res);

		if(!isset($res[1]))
			return $url;
		
		return $res[1];
	}
	
	public static function getVimeoIdByUrl($url)
	{
		$regexstr = '~
			# Match Vimeo link and embed code
			(?:&lt;iframe [^&gt;]*src=")?		# If iframe match up to first quote of src
			(?:							# Group vimeo url
				https?:\/\/				# Either http or https
				(?:[\w]+\.)*			# Optional subdomains
				vimeo\.com				# Match vimeo.com
				(?:[\/\w]*\/videos?)?	# Optional video sub directory this handles groups links also
				\/						# Slash before Id
				([0-9]+)				# $1: VIDEO_ID is numeric
				[^\s]*					# Not a space
			)							# End group
			"?							# Match end quote if part of src
			(?:[^&gt;]*&gt;&lt;/iframe&gt;)?		# Match the end of the iframe
			(?:&lt;p&gt;.*&lt;/p&gt;)?		        # Match any title information stuff
			~ix';
		
		preg_match($regexstr, $url, $matches);
		
		if (!isset($matches[1])) {
			if (substr($url, -1) == '/') {
				 $url = substr($url, 0, -1);
			}
			$expl = explode('/', $url);
			return end($expl);
		} else {
			return $matches[1];
		}
	}
	
	public static function getVimeoImageById($id)
	{
		$data = @file_get_contents("http://vimeo.com/api/v2/video/{$id}.php");
		if (empty($data)) {
			return '';
		}
		
		$hash = unserialize($data);
		return $hash[0]['thumbnail_large'];
	}	
	
	public static function getCoubIdByUrl($url)
	{
		if (substr($url, -1) == '/') {
			$url = substr($url, 0, -1);
		}
		$expl = explode('/', $url);
		return end($expl);
	}
	
	public static function getCoubImageById($id)
	{
		$data = @file_get_contents("http://coub.com/api/v2/coubs/{$id}");
		if (empty($data)) {
			return '';
		}		
		
		$hash = json_decode($data, 1);
		return $hash['timeline_picture'];
	}	
	
}