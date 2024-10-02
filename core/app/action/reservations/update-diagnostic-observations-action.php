<?php
if(count($_POST)>0){
      $reservation = ReservationData::getById($_POST["reservationId"]);

      if($reservation){
         $reservation->diagnostic_observations = $_POST["diagnosticObservations"];
         
         $updatedReservation = $reservation->updateDiagnosticObservations();
         if($updatedReservation  && $updatedReservation[0]) return $reservation;
         else return http_response_code(500);
      } 
}
else{
   return http_response_code(500);
}
?>