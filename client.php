<?php
/**
*	Файл который вызывается по JSON и посылает cURL запрос на сервер, 
*	откуда и возвращается в $result ответ от системы со статусом.
*	В этом же файле потом ответ и распознаётся.
*/
include_once "./functions.php";
$data = [
	"firstName" => "Vasya",
	"lastName" => "Pupkin",
	"dateOfBirth" => "1984-07-31",
	"Salary" => "1000",
	"creditScore" => "good"
];
$url = "http://Phonexa.test/server/";			//	адрес отправки запроса

if ( isset ($_POST['creditScore'] ) ) 
	{	//	логика для подмены данных 
		switch ($_POST['creditScore']) {
					case "good": $data['creditScore'] = "good"; $data['Salary'] = "700";  break;
					case "bad": $data['creditScore'] = $_POST['creditScore']; $data['Salary'] = "300";   break;
					case "error": $data['creditScore'] = "error"; break;
				  }
	}

	switch ( $_POST['typeRequest'] ) { //  узнаём какие данные пришли JSON или XML
		case "JSON": 	
				$data_json = json_encode( [ "userInfo" => $data ] );

				//	отправка данных на сервер
				if( $curl = curl_init() ){
				curl_setopt($curl, CURLOPT_URL, $url); 
				curl_setopt($curl, CURLOPT_POSTFIELDS, "jsonRequest=" . $data_json);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				$result = curl_exec($curl);	
				curl_close($curl);
				} else { echo "<br>No curl_init"; }
		break;


		case "XML": 
			$data2 = $data;
			unset ($data2['dateOfBirth']);	
			$data2['age'] = date('Y-m-d' ,time()) - date('Y-m-d' ,strtotime($data['dateOfBirth']));
			$input_xml = generateXML( $data2, false );		//	генерируем сам XML
			
			//	отправка данных на сервер
			if( $curl = curl_init() ){
			curl_setopt($curl, CURLOPT_URL, $url);                                                                                                                             
			curl_setopt($curl, CURLOPT_POSTFIELDS, "xmlRequest=" . $input_xml);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			$result = htmlspecialchars_decode(curl_exec($curl));	
			curl_close($curl);
			} else { echo "<br>No curl_init"; }
		break;					
	}	
	
	

	
	
/** 
*	response  analysis
*/

if ( isset ( $result ) ) {
	//	Распознаём тип запроса
	if ( preg_match ("/\{.*?\}/", $result ) ) {	
		$jsonArray = json_decode($result, true); //		декодируем стороку чтобы определить статус

		switch ($jsonArray['SubmitDataResult']) {
			case 'success': $resultResponse = "Sold (успешная продажа)"; break;
			case 'reject': $resultResponse = "Reject (заявка отклонена, низкий кредитный рейтинг)"; break;
			case 'error': $resultResponse = "Error (причина ошибки - [{$jsonArray['SubmitDataErrorMessage']}])"; break;
			default: $resultResponse = "Error"; break;
		}


	} elseif ( preg_match ("/xml/", htmlspecialchars_decode($result) ) ) {
		$returnCodeDescription = getReturnCodeDescription( $result, "returnCodeDescription" );	//		парсим XML чтобы определить статус из returnCodeDescription
		
		switch ($returnCodeDescription) {
			case 'SUCCESS': $resultResponse = "Sold (успешная продажа)"; break;
			case 'REJECT':  $resultResponse = "Reject (заявка отклонена, низкий кредитный рейтинг)"; break;
			case 'ERROR':   $SubmitDataErrorMessage = getReturnCodeDescription( $result, "SubmitDataErrorMessage");
							$resultResponse = "Error (причина ошибки - [{$SubmitDataErrorMessage}])"; 
							break;
			default: $resultResponse = "Error"; break;
		}		
		
	} else {
			echo "<br>Не сработал preg_match JSON";	
		}




//	выводим определённый статус
	if ( isset ($resultResponse) ) {
			echo "<br>Был определён статус - <strong>" . $resultResponse . "</strong>";
	} else {
		echo "<br>Статус не определён, ошибка.";
	}
} else { echo "<br>Какая-то ошибка, Response не пришел.";}
	
	// Response (для отладки)
		if ( isset ( $result ) ) echo "<br><br><br><br>Response (отладочная информация): <br>" . htmlspecialchars($result);
	
	

?>
