<?php

	/**
	*	Генерация XML
	*	$date - массив с элементамии и их значениями
	*	$encoding - наличие атрибута encoding="UTF-8"
	*	return XML string
	*/
		function generateXML( $date, $encoding = true ){
			   // Создаём XML-документ версии 1.0 с кодировкой utf-8  
				if ( $encoding == true ) $dom = new domDocument("1.0", "UTF-8"); 
								   else  $dom = new domDocument("1.0"); 
					//добавление корня - <userInfo> 
					$root = $dom->appendChild($dom->createElement('userInfo'));
					$root->setAttribute("version", "1.6");
					
					foreach ( $date as $key => $value ) {
						$key = $root->appendChild( $dom->createElement( $key, $value ) );
					}
				//генерация xml 
					$dom->formatOutput = true; // установка атрибута formatOutput
				// save XML as string or file 
			return $result = $dom->saveXML(); // передача строки в test1 
		}	
	
	
	
	/**
	*	Парсинг XML, поиск по названию свойства
	*	$date - XML string
	*	return значение returnCodeDescription
	*/	
	function getReturnCodeDescription( $date, $value )
	{
			$xml = simplexml_load_string( $date );
			$key = $value;
			foreach($xml->xpath('/userInfo') as $item){
							$result = $item->$key;
				}
			if (!isset ($result))	return NULL;
			return $result;
	}
		
		
?>
