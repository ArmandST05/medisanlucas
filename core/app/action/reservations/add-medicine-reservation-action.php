<?php
if(count($_POST)>0){
      $medicine = MedicineData::getByMedicineReservation($_POST["reservationId"],$_POST["medicineId"]);
      if(isset($medicine)){
         return http_response_code(500);
      }
      else{
         $medicine = new MedicineData();
         $medicine->reservation_id = $_POST["reservationId"];
         $medicine->medicine_id = $_POST["medicineId"];

         $newMedicine = $medicine->addByReservation();
         if($newMedicine) echo $newMedicine[1];
         else return http_response_code(500);
      }     
}
else{
   return http_response_code(500);
}
?>