<?php
/*
* Класс для сравнения двух текстов
*/


class Diff {
	
	public $step_empty; //Пропущенно шагов
	
	public $text1; //Первый текст	
	
	public $text2; //Второй текст
	
	public $text_result; //Результирующий массив
	
	public $bad_index; //Индекс, когда ничего не находим
	
	public $statuses; //Цвета
	
	
	/*Логика скрипта*/
	function __construct($text1,$text2)
	{
		$this->text1 = $this->getArray($text1);
		$this->text2 = $this->getArray($text2);
		$this->bad_index = -1;
		$this->statuses = ['equal'=>'white','old'=>'danger','new'=>'success','change'=>'warning'];
		
		
		foreach($this->text2 as $k2 => $t2)
		{			
			$t2 = $this->stripWhitespaces($t2);
			$this->size1 = count($this->text1)-1;
			foreach($this->text1 as $k => $t1)
			{				
				$t1 = $this->stripWhitespaces($t1);
				//Проверяем три состояния: полное совпадение, частичное и полное несовпадение				
				
				//Полное совпадение
				if ($t1==$t2){				
					$this->text_result[] = ['status'=>'equal','text'=>$t1];
					$this->checkFirstText($k);
					break;
				}
				
				//Проверяем схожесть строк 
				similar_text($t1, $t2, $perc);
				
				//Если почти похожи
				if (($perc>=50) && ($perc<100)){	
					$this->text_result[] = ['status'=>'change','text'=>$t2,'last'=>$t1,'perc'=>$perc];
					$this->checkFirstText($k);
					break;
				}
				
				//Прошли весь массив и не нашли что искали
				if ($k==$this->size1){
					$this->text_result[] = ['status'=>'new','text'=>$t2];	
					if ($this->bad_index==-1) $this->bad_index = $k2+1;
				}
			}
		}
		if (count($this->text1)) foreach($this->text1 as $t) $this->text_result[] = ['status'=>'old','text'=>$t];
	}	
	
	
	/*Убираем непечатыемы символы*/
	function stripWhitespaces($string) 
	{
		return preg_replace('/[\x00-\x1F\x7F]/u', '', $string);
	}
	
	
	/*Разбиваем текст на массив строк*/
	function getArray($text)
	{
		return explode(PHP_EOL,$text);
	}
	
	
	/*Производим манипуляции с первым текстом*/
	function checkFirstText($k)
	{		
		unset($this->text1[$k]);		
		if ($k>0){
			$tmp = [];
			for($i=0;$i<$k;$i++){
				$tmp[] = ['status'=>'old','text'=>$this->text1[$i]];
				unset($this->text1[$i]);
			}						
			array_splice($this->text_result, $this->bad_index, 0, $tmp);			
		}
		$this->text1 = $this->repairArray($this->text1);
		$this->bad_index=-1;
	}
	
	
	//Сбрасываем индексы
	function repairArray($arr)
	{		
		if ((!is_array) || (!count($arr))) return [];
		$tmp = [];		
		foreach($arr as $t) $tmp[] = $t;
		return $tmp;
	}
	
	
	/*Отдаем массив*/
	function getAnswer()
	{
		return $this->text_result;
	}	
	
	
	//Ответ в виде таблички
	function getTable()
	{
		$result='<h2>Результат:</h2>
		<table width="100%">';
		foreach($this->text_result as $str){
			if (isset($str['last'])) $last = '<div class="last alert alert-default">'.$str['last'].'</div>';
			else $last = '';
			$result.= '<tr><td class="alert alert-'.$this->statuses[$str['status']].'">'.$str['text'].$last.'</td></tr>';		
		}
		$result.= '</table>';
		return $result;
	}
}
