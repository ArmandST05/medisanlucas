<?php
if(count($_POST)>0){
	$explorationExam = ExplorationExamData::getByReservation($_POST["id"]);
	if($explorationExam->deleteByReservation()) return http_response_code(200);
	else return http_response_code(500);
}
?>