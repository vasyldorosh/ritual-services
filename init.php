<?php 

/*
$dirs = [
	'/runtime' 				=> '0777',
	'/web/assets' 			=> '0777',
	'/web/files' 			=> '0775',
	'/web/files/structure/' => '0775',
	'/web/upload' 			=> '0775',
	'/web/upload/editor' 	=> '0775',
	'/web/forum/forum' 		=> '0775',
];

foreach ($dirs as $dir=>$chmod) {
	$d = __DIR__ . $dir;
	if (!is_dir($d)) {
		mkdir($d);
	}
	
	chmod($d, $chmod);
	chown($d, 'www-data');
}
*/

?>