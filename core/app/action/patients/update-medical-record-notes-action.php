<?php
if(count($_POST)>0){
      $patient = PatientData::getById($_POST["patientId"]);

      if($patient){
         $patient->notes = $_POST["notes"];
         if($patient->updateNotes()) return http_response_code(200);
         else return http_response_code(500);
      } 
}
else{
   return http_response_code(500);
}
?>