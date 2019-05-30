<?php
/**
*	Серверная часть, после получения POST данных формирует 
*	и выводит соответствующие данных в зависимости от XML or JSON,
*	которые возвращаются пользователя в которых уже присутствует статус.
*
*/
include_once "../functions.php";
if ( isset ($_REQUEST) ) {
	if ( isset ($_REQUEST['xmlRequest'] ) ) {	  	//	Обрабатываем полученный xml
			$creditScore = getReturnCodeDescription( $_REQUEST['xmlRequest'], 'creditScore' );
			 $errorMessage= "Lead not Found";
				switch ( $creditScore ) {		//	реагируем на соответствующий статус и формируем ответ
					case "good": $dateXML = ['returnCode'=> "1", 'returnCodeDescription'=> "SUCCESS", 'transactionId'=> "AC158457A86E711D0000016AB036886A03E7" ]; break;
					case "bad": $dateXML = ['returnCode'=> "0", 'returnCodeDescription'=> "REJECT"]; break;
					case "error": $dateXML = ['returnCode'=> "0", 'returnCodeDescription'=> "ERROR", 'SubmitDataErrorMessage'=>$errorMessage]; break;
					default: $dateXML = ['returnCode'=> "0", 'returnCodeDescription'=> "ERROR", 'returnError'=> $errorMessage ]; break;
					}
				
			$XML = generateXML( $dateXML );	//	генерируем XML ответ
			echo htmlspecialchars( $XML );		
			
			
			
	} elseif ( isset ($_REQUEST['jsonRequest'] ) ) {	//	Обрабатываем полученный json
			$jsonArray = json_decode($_REQUEST['jsonRequest'], true);	//		декодируем строку в массив
			$errorMessage= "";
				
			switch ( $jsonArray['userInfo']['creditScore'] ) {	//	реагируем на соответствующий статус и формируем ответ
					case "good": $respons= ['SubmitDataResult'=>"success"]; break;
					case "bad": $respons = ['SubmitDataResult'=> "reject"]; break;
					case "error": $respons = ['SubmitDataResult'=> "error", 'SubmitDataErrorMessage'=>$errorMessage]; break;
				    default: $respons = ['SubmitDataResult'=>"error", 'SubmitDataErrorMessage'=>$errorMessage ]; break;
			}	
					echo json_encode($respons);
			}	 else { echo "<br>_POST пуст"; }
	} else { echo "<br>_POST пуст"; }
	


?>

