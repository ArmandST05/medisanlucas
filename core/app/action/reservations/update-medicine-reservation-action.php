<?php
if(count($_POST)>0){
   $medicine = MedicineData::getByReservationId($_POST["id"]);
   $medicine->column = $_POST["column"];
   $medicine->value = $_POST["value"];

   if($medicine->updateByReservation())echo $medicine->id;
   else return http_response_code(500);
}else return http_response_code(500);
?>