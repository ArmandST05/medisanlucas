<?php
    $reservationId = (count($_POST)>0) ? $_POST["id"]: $_GET["id"];
    $reservation = ReservationData::getById($reservationId);
    $deleted = $reservation->delete();

    if(
        !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
     ){
        # Ejecuta si la petición es a través de AJAX.
        if($deleted){
            return $reservation;
        }
        else {
            return http_response_code(500);
        }
     }else{
        # Ejecuta si la petición NO es a través de AJAX.
        print "<script>history.back();</script>";
     }
?>