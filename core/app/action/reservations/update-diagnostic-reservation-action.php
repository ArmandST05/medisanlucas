<?php
if(count($_POST)>0){
   $diagnostic = DiagnosticData::getByReservation($_POST["id"]);

   $diagnostic->value = $_POST["value"];
   if($diagnostic->updateByReservation())echo $diagnostic->id;
   else return http_response_code(500);
}else return http_response_code(500);
?>