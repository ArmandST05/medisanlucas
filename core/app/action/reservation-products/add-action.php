<?php
if(count($_POST) > 0){
    $newProduct = new ReservationData();
    $newProduct->product_id = $_POST["productId"];
    $newProduct->reservation_id = $_POST["reservationId"];
    $addedProduct = $newProduct->addProduct();

    if($addedProduct && $addedProduct[0]){
        return http_response_code(200);
    }
    else{
        return http_response_code(500);
    }
}else{
    return http_response_code(500);
}
?>