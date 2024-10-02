<?php
if(count($_POST)>0){
      $reservation = ReservationData::getById($_POST["reservationId"]);

      if($reservation){
         $reservation->reason = $_POST["reason"];
         
         if($reservation->updateReason()) return $reservation;
         else return http_response_code(500);
      } 
}
else{
   return http_response_code(500);
}
?>