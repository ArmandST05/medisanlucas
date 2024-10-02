<?php
if(count($_POST) > 0){
    $deletedProduct = ReservationData::deleteProductByReservation($_POST["reservationId"],$_POST["productId"]);

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