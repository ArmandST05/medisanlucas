<?php
if(count($_POST)>0){
      $reservation = ReservationData::getById($_POST["reservationId"]);

      if($reservation){
         $reservation->physical_observations = $_POST["physicalObservations"];
         $updatedReservation = $reservation->updatePhysicalObservations();
         if($updatedReservation  && $updatedReservation[0]) return $reservation;
         else return http_response_code(500);
      } 
}
else{
   return http_response_code(500);
}
?>