<?php
if(count($_POST)>0){
      $reservation = ReservationData::getById($_POST["reservationId"]);

      if($reservation){
         $reservation->patient_observations = $_POST["patientObservations"];
         $updatedReservation = $reservation->updatePatientObservations();
         if($updatedReservation  && $updatedReservation[0]) return $reservation;
         else return http_response_code(500);
      } 
}
else{
   return http_response_code(500);
}
?>