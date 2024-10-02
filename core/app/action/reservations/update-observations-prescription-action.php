<?php
if(count($_POST)>0){
      $reservation = ReservationData::getById($_POST["reservationId"]);

      if($reservation){
         $reservation->observations_prescription = $_POST["observations"];
         
         if($reservation->updateObservationsPrescription()) return $reservation;
         else return http_response_code(500);
      } 
}
else{
   return http_response_code(500);
}
?>