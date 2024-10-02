<?php
if(count($_POST)>0){
      $reservation = ReservationData::getById($_POST["reservationId"]);

      if($reservation){
         $reservation->reservation_id = $_POST["reservationId"];
         $reservation->status_id = $_POST["statusId"];

         if($reservation->updateStatus()){
            $status = ['id' => $reservation->status_id,'name' =>$reservation->getStatus()->name];
            echo json_encode($status);
         }
         else return http_response_code(500);
      } 
}
else{
   return http_response_code(500);
}
?>