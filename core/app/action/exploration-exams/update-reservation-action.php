<?php
if(count($_POST)>0){
      $explorationExam = ExplorationExamData::getByReservation($_POST["id"]);
      $explorationExam->value = $_POST["value"];

      if($explorationExam->updateByReservation()) return $explorationExam->value;
      else return http_response_code(500);
}
else return http_response_code(500);
?>