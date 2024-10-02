<?php
date_default_timezone_set('America/Mexico_City');
if(count($_POST)>0){
      $reservation = ReservationData::getById($_POST["reservationId"]);

      if($reservation){
         $reservation->reservation_id = $_POST["reservationId"];
         $reservation->date_at_final = date("Y-m-d H:i:00");

         if($reservation->finish()){
            return http_response_code(200);
         }
         else return http_response_code(500);
      } 
}
else{
   return http_response_code(500);
}
?>