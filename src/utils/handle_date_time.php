<?php

class handleDateTime{
	
	public function getCurrentDateTimeInt(){
		date_default_timezone_set('America/Montevideo');
		$dateTime = date('m-d-Y h:i:s a', time());
		return substr($dateTime, 6, 4) . substr($dateTime, 0, 2) . substr($dateTime, 3, 2) . substr($dateTime,11,2) . substr($dateTime, 14, 2) . substr($dateTime, 17, 2);
	}

	public function getDateInt(){
		$onlyDate = explode(" ", $date);
		if(strpos(substr($onlyDate[0],0,4), "/") || strpos(substr($onlyDate[0],0,4),"-"))
			return substr($onlyDate[0], 6, 4) . substr($onlyDate[0], 3, 2) . substr($onlyDate[0],0,2);
		else
			return substr($onlyDate[0], 0, 4) . substr($onlyDate[0], 5, 2) . substr($onlyDate[0], 8, 2);
	}

	public function setFormatBarDate($intDate){
		return  substr($intDate, 6, 2) . "/" .  substr($intDate, 4, 2) . "/" . substr($intDate, 0, 4);
	}

	public function setFormatBarDateAAAAMM($intDate){
		return  substr($intDate, 4, 2) . "/" . substr($intDate, 0, 4);
	}

	public function setFormatBarDateMMAAAA($intDate){ // from AAAA-MM to: MM/AAAA
		return  substr($intDate, 5, 2) . "/" . substr($intDate, 0, 4);
	}

	public function setFormatBarDateTime($intDateTime){
		return substr($intDateTime, 6, 2) . "/" .  substr($intDateTime, 4, 2) . "/" . substr($intDateTime, 0, 4) . " " . substr($intDateTime, 8, 2) . ":" . substr($intDateTime, 10, 2) . ":" . substr($intDateTime, 12, 2);
	}

	public function setFormatHTMLDate(){
		return  substr($intDate, 0, 4) . "-" .  substr($intDate, 4, 2) . "-" . substr($intDate, 6, 2);
	}

	public function setFormatHTMLDateTime($intDateTime){
		return substr($intDateTime, 0, 4) . "-" .  substr($intDateTime, 4, 2) . "-" . substr($intDateTime, 6, 2) . " " . substr($intDateTime, 8, 2) . ":" . substr($intDateTime, 10, 2) . ":" . substr($intDateTime, 12, 2);
	}

	public function getDateTimeInt($dateTime){
		return substr($dateTime, 0, 4) . substr($dateTime, 5, 2) . substr($dateTime, 8, 2) . substr($dateTime, 11,2) . substr($dateTime, 14, 2) . substr($dateTime, 17, 2);
	}

	public function getNextTimeInt($newValue){
		$newTime = strtotime ('+14 minute' , strtotime ($newValue)) ;
		$newTime = date ('Y-m-d H:i:s' , $newTime);
		return handleDateTime::getDateTimeInt($newTime);
	}

	public function isTimeToChangeToken($nextChange){
		$currentDateTime = handleDateTime::getCurrentDateTimeInt();

		if($nextChange <= $currentDateTime)
			return 2;
		else if($nextChange > $currentDateTime)
			return 0;

		return 1;
	}

	public function sameMonth($fecha1, $fecha2){ // formato AAAAMMDD
		if(substr($fecha1, 0, 4) != substr($fecha2, 0, 4)) // Distinto año
			return false;
		// Mismo año
		if(substr($fecha1, 4, 6) != substr($fecha2, 4, 6)) // Distinto mes
			return false;
		return true;
	}

	public function getDateAAAAMMDD(){ // Return AAAAMMDD
		$newTime = date ('Y-m-d');
		return str_replace('-', '', $newTime);
	}
	public function setFormatBDFromHTML($fechaHTML){ // Return AAAAMMDD
		return str_replace('-', '', $fechaHTML);
	}
	public function setFormatHTMLFromBD($fechaBD){ // Return AAAA-MM-DD
		return substr($fechaBD, 0, 4) . "-" .  substr($fechaBD, 4, 2) . "-" . substr($fechaBD, 6, 2);
	}
	public function setFormatHTMLAAMMFromBD($fechaBD){ // Return AA-MM-DD
		return substr($fechaBD, 0, 2) . "-" .  substr($fechaBD, 2, 2);
	}
	public function setFormatHTMLAAAAMMFromBD($fechaBD){ // Return AAAA-MM
		return substr($fechaBD, 0, 4) . "-" .  substr($fechaBD, 4, 2);
	}
	public function setFormatAAMMDDFromBD($fechaBD){ // Return AAMMDD
		return substr($fechaBD, 2, 8);
	}
	public function setFormatAAMMDDFromHTML($fechaHTML){ // Return AAMMDD
		$respuesta = str_replace('-', '', $fechaHTML);
		return substr($respuesta, 2, 6);
	}
	public function setFormatDDMMAAFromHTML($fechaHTML){ // Return DDMMAA
		$respuesta = str_replace('-', '', $fechaHTML);
		$respuesta = substr($respuesta, 2, 6); 
		$respuesta = substr($respuesta, 4, 2) . substr($respuesta, 2, 2) . substr($respuesta, 0, 2);
		return $respuesta;
	}
	public function setFormatMMAAFromBD($fechaDB){ // Return MMAA
		$respuesta = str_replace('-', '', $fechaDB);
		$respuesta = substr($respuesta, 2, 4); 
		$respuesta = substr($respuesta, 2, 2) . substr($respuesta, 0, 2);
		return $respuesta;
	}
	public function setFormatAAAAMMFromMMAA($fecha){ // Return AAAAMM funciona a partir del 2000 
		$respuesta = "20" . substr($fecha, 2, 4) . substr($fecha, 0, 2);
		return $respuesta;
	}
	public function setFormatAAMMFromHTML($fechaHTML){ // Return AAMM
		$respuesta = str_replace('-', '', $fechaHTML);
		// $respuesta = substr($respuesta, 2, 6);
		return substr($respuesta, 2, 4);
		// return substr($fechaHTML, 2, 4) . substr($fechaHTML, 5, 2);
		// return $respuesta;
	}
	public function setFormatMMAAAAFromHTML($fechaHTML){ // Return MMAAAA from AAAA-MM
		$respuesta = str_replace('-', '', $fechaHTML);
		// $respuesta = substr($respuesta, 2, 6);s
		return substr($respuesta, 4, 2) . substr($respuesta, 0, 4);
		// return substr($fechaHTML, 2, 4) . substr($fechaHTML, 5, 2);
		// return $respuesta;
	}

	public function setFormatAAAAMMFromHTML($fechaHTML){ // Return AAAAMM
		$respuesta = str_replace('-', '', $fechaHTML);
		// $respuesta = substr($respuesta, 2, 6);
		return substr($respuesta, 0, 6);
		// return substr($fechaHTML, 2, 4) . substr($fechaHTML, 5, 2);
		// return $respuesta;
	}
	
	public function getCurrentDateAAAAMM(){
		date_default_timezone_set('America/Montevideo');
		$dateTime = date('Ym');
		return $dateTime;
	}

}