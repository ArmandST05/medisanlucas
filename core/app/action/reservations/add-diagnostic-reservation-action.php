<?php
if(count($_POST)>0){
      $diagnostic = DiagnosticData::getByDiagnosticReservation($_POST["reservationId"],$_POST["diagnosticId"]);

      if($diagnostic){
         return http_response_code(500);
      }
      else{
         $diagnostic = new DiagnosticData();
         $diagnostic->reservation_id = $_POST["reservationId"];
         $diagnostic->diagnostic_id = $_POST["diagnosticId"];

         $newDiagnostic = $diagnostic->addByReservation();
         if($newDiagnostic) echo $newDiagnostic[1];
         else return http_response_code(500);
      }     
}
else{
   return http_response_code(500);
}
?>