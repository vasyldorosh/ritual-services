<?

    function translit($string, $reverse = false)
    {
        $table = array(
            'А' => 'A',
            'Б' => 'B',
            'В' => 'V',
            'Г' => 'G',
            'Д' => 'D',
            'Е' => 'E',
            'Ё' => 'YO',
            'Ж' => 'ZH',
            'З' => 'Z',
            'И' => 'I',
            'Й' => 'J',
            'К' => 'K',
            'Л' => 'L',
            'М' => 'M',
            'Н' => 'N',
            'О' => 'O',
            'П' => 'P',
            'Р' => 'R',
            'С' => 'S',
            'Т' => 'T',
            'У' => 'U',
            'Ф' => 'F',
            'Х' => 'H',
            'Ц' => 'C',
            'Ч' => 'CH',
            'Ш' => 'SH',
            'Щ' => 'CSH',
            'Ь' => '',
            'Ы' => 'Y',
            'Ъ' => '',
            'Э' => 'E',
            'Ю' => 'YU',
            'Я' => 'YA',

            'а' => 'a',
            'б' => 'b',
            'в' => 'v',
            'г' => 'g',
            'д' => 'd',
            'е' => 'e',
            'ё' => 'yo',
            'ж' => 'zh',
            'з' => 'z',
            'и' => 'i',
            'й' => 'j',
            'к' => 'k',
            'л' => 'l',
            'м' => 'm',
            'н' => 'n',
            'о' => 'o',
            'п' => 'p',
            'р' => 'r',
            'с' => 's',
            'т' => 't',
            'у' => 'u',
            'ф' => 'f',
            'х' => 'h',
            'ц' => 'c',
            'ч' => 'ch',
            'ш' => 'sh',
            'щ' => 'csh',
            'ь' => '',
            'ы' => 'y',
            'ъ' => '',
            'э' => 'e',
            'ю' => 'yu',
            'я' => 'ya',
            
            'ї' => 'yi',
            'і' => 'i',
            'є' => 'ye',
        );

        $output = str_replace(
            array_keys($table),
            array_values($table),$string
        );

        return $output;
    }


function getex($filename) {
	return end(explode(".", $filename));
}

function t($value) {
	$lang = isset($_GET['lang']) ? $_GET['lang'] : 'ru';
	
	$translator = [
		'en' => [
			'Вы не выбрали файл' => 'You did not select a file',
			'Размер файла не соответствует нормам' => 'File size does not conform',
			'Допускается загрузка только картинок JPG, PNG, GIF.' => 'Allowed to retrieve only JPG images, PNG, GIF.',
			'Что то пошло не так. Попытайтесь загрузить файл ещё раз.' => 'Something went wrong. Try downloading the file again.',
			'Файл' => 'File',
			'загружен' => 'uploaded',
		],
		'pl' => [
			'Вы не выбрали файл' => 'Nie wybrano pliku',
			'Размер файла не соответствует нормам' => 'Rozmiar pliku nie odpowiada',
			'Допускается загрузка только картинок JPG, PNG, GIF.' => 'Pozwolono, aby pobrać tylko pliki JPG, PNG, GIF.',
			'Что то пошло не так. Попытайтесь загрузить файл ещё раз.' => 'Coś poszło nie tak. Spróbuj ponownie pobrać plik.',
			'Файл' => 'Plik',
			'загружен' => 'załadowany',
		],
	];
	
	if (isset($translator[$lang][$value])) {
		return $translator[$lang][$value];
	} else {
		return $value;
	}
}

if($_FILES['upload']) {
	if (($_FILES['upload'] == "none") OR (empty($_FILES['upload']['name'])) ){
		$message = t('Вы не выбрали файл');
	} else if ($_FILES['upload']["size"] == 0 OR $_FILES['upload']["size"] > 2050000*2) {
		$message = t('Размер файла не соответствует нормам');
	} else if (($_FILES['upload']["type"] != "image/jpeg") AND ($_FILES['upload']["type"] != "image/gif") AND ($_FILES['upload']["type"] != "image/png")) {
		$message = t('Допускается загрузка только картинок JPG, PNG, GIF.');
	} else if (!is_uploaded_file($_FILES['upload']["tmp_name"])) {
		$message = t('Что то пошло не так. Попытайтесь загрузить файл ещё раз.');
	} else{
		$docRoot = $_SERVER['DOCUMENT_ROOT'];
		$name =rand(1, 1000).md5(translit($_FILES['upload']['name'])).'.'.getex($_FILES['upload']['name']);

		$path = $docRoot . '/upload/' . substr($name,0,2);
        if (!is_dir($path)){
			mkdir($path);
			
			$path.= '/'.substr($name,2,2);
			if (!is_dir($path)){
				mkdir($path);
			}		
        }
		$file = '/upload/'. substr($name,0,2) . '/' . substr($name,2,2) . '/' . $name;
		
		move_uploaded_file($_FILES['upload']['tmp_name'], $docRoot . $file);
		$message = t('Файл') . ' ' .$_FILES['upload']['name']. ' ' . t('загружен');
	}
	$callback = $_REQUEST['CKEditorFuncNum'];
	echo '<script type="text/javascript">window.parent.CKEDITOR.tools.callFunction("'.$callback.'", "'.$file.'", "'.$message.'" );</script>';
}
?>