<?php
if(count($_POST)>0){
	//Validar si ya se agregó ese examen a la cita.
	$existsExplorationExam = ExplorationExamData::validateByReservation($_POST["reservationId"],$_POST["explorationExamId"]);
	if(isset($existsExplorationExam)){
		return http_response_code(500);
	}
	else{
		$explorationExam = new ExplorationExamData();
		$explorationExam->exploration_exam_id = $_POST["explorationExamId"];
		$explorationExam->reservation_id = $_POST["reservationId"];
		$explorationExam->value = "";
		if($explorationExam->addByReservation()) return http_response_code(200);
		else return http_response_code(500);
	}
}
?>