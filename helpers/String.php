<?php
namespace app\helpers;

/**
 * 
 * Помощник String
 */
class String
{	
	public static function deleteLast($string, $symbol)
	{
		if (substr($string, -1) == $symbol) {
			 $string = substr($string, 0, -1);
		}
		
		return $string;
	}
	
    /**
     * Обрезка UTF строки
     * 
     * @static
     * @param string $string
     * @param int $length
     * @param string $postfix
     * @return bool|string
     */
    public static function truncate($string, $length = 50, $breakWords = false, $postfix = '...')
    {
        $wordEndChars = ',.?!;:'; /* символы окончания слова */
        $truncated = trim($string);
        $length = (int)$length;
        if (!$string) {
            return $truncated;
        }
        $fullLength = iconv_strlen($truncated, 'UTF-8');
        if ($fullLength > $length) {
            $truncated = trim(iconv_substr($truncated, 0, $length, 'UTF-8'));
            if (!$breakWords) {
                $words = explode(' ', $truncated);
                $wordCount = sizeof($words);
                if (rtrim($words[$wordCount-1], $wordEndChars) == $words[$wordCount-1]) {
                    unset($words[$wordCount-1]);
                }
                $wordCount = sizeof($words);
                if (!empty($words[$wordCount-1])) {
                	$words[$wordCount-1] = rtrim($words[$wordCount-1], $wordEndChars);	
                }
                $truncated = implode(' ', $words);
            }
            $truncated .= $postfix;
        }
        return $truncated;
    }
	

}