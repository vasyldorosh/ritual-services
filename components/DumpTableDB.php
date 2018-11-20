<?php

namespace app\components;

use Yii;

class DumpTableDB
{         
	/**
	 * Create table dump
	 * @param $tableName
	 * @return mixed
	 */
	public static function get($tableName)
	{
		$db = Yii::$app->db;
		$pdo = $db->pdo;

		$rows = $db->createCommand('SELECT * FROM '.$db->quoteTableName($tableName).';')->queryAll();

                    
		if(empty($rows))
			return;
		
		$content = '';
		
		echo PHP_EOL."--\n-- Data for table `$tableName`\n--".PHP_EOL.PHP_EOL;

		$attrs = array_map(array($db, 'quoteColumnName'), array_keys($rows[0]));
		echo 'INSERT INTO '.$db->quoteTableName($tableName).''." (", implode(', ', $attrs), ') VALUES'.PHP_EOL;
		$i=0;
		$rowsCount = count($rows);
		foreach($rows AS $row)
		{
			// Process row
			foreach($row AS $key => $value)
			{
				if($value === null)
					$row[$key] = 'NULL';
				else
					$row[$key] = $pdo->quote($value);
			}

			echo " (", implode(', ', $row), ')';
			if($i<$rowsCount-1)
				echo ',';
			else
				echo ';';
			echo PHP_EOL;
			$i++;
		}
		echo PHP_EOL;
		echo PHP_EOL;
	}
	
	public static function import($file)
    {
		$pdo = Yii::$app->db->pdo;
        try 
        { 
            if (file_exists($file)) 
            {
                $pdo->exec(file_get_contents($file));
                return true;
            } 
        } 
        catch (PDOException $e) 
        { 
            echo $e->getMessage();
            exit; 
        }
    }	

}