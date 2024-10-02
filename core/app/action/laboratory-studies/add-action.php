<?php
if(count($_POST)>0){
	$validateStudy = PatientLaboratoryStudyData::validateByStudyTypeReservation($_POST["reservationId"],$_POST["laboratoryStudyId"]);
	$reservation = ReservationData::getById($_POST["reservationId"]);
	if($validateStudy){//El estudio ya existe
		return http_response_code(500);
	}else{
		$study = new PatientLaboratoryStudyData();
		$study->patient_id = $reservation->patient_id;
		$study->reservation_id = $_POST["reservationId"];
		$study->laboratory_study_id = $_POST["laboratoryStudyId"];
		$study->date = $_POST["date"];
		
		$newStudy = $study->add();

		if($newStudy && $newStudy[0]){
			echo ($newStudy[1]);
			return http_response_code(200);
		}else{
			return http_response_code(500);
		}
	}
}else{
	return http_response_code(500);
}
?>