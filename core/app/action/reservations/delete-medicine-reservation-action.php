<?php
$medicine = new MedicineData();
if($medicine->deleteByReservation($_POST["reservationMedicineId"])) return $medicine;
else return http_response_code(500);

?>