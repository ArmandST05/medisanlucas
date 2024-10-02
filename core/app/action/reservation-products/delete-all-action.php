<?php
if(count($_POST) > 0){
    $deletedProduct = ReservationData::deleteAllProductsByReservation($_POST["reservationId"]);

    if($deletedProduct){
        return http_response_code(200);
    }
    else{
        return http_response_code(500);
    }
}else{
    return http_response_code(500);
}
?>