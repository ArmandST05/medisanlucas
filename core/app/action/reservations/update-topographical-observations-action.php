<?php
if(count($_POST)>0){
      $reservation = ReservationData::getById($_POST["reservationId"]);

      if($reservation){
         $reservation->topographical_observations = $_POST["topographicalObservations"];
         $updatedReservation = $reservation->updateTopographicalObservations();
         if($updatedReservation  && $updatedReservation[0]) return $reservation;
         else return http_response_code(500);
      } 
}
else{
   return http_response_code(500);
}
?>