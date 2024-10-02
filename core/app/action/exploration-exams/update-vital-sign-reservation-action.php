<?php
if(count($_POST)>0){
      $existsExplorationExam = ExplorationExamData::validateByReservation($_POST["reservationId"],$_POST["explorationExamId"]);

	if(isset($existsExplorationExam)){
            $existsExplorationExam->value = $_POST["value"];

            if($existsExplorationExam->updateByReservation()) return $existsExplorationExam->value;
            else return http_response_code(500);
      
	}
	else{
		$explorationExam = new ExplorationExamData();
		$explorationExam->exploration_exam_id = $_POST["explorationExamId"];
		$explorationExam->reservation_id = $_POST["reservationId"];
		$explorationExam->value = $_POST["value"];
		if($explorationExam->addByReservation()) return $_POST["value"];
		else return http_response_code(500);
	}

}
else return http_response_code(500);
?>