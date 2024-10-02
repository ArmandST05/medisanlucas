<?php
if(count($_POST)>0){
   $dateAt = $_POST["date"]." ".$_POST["timeAt"];
   $repeatedLaboratory = ReservationData::getRepeatedLaboratory($dateAt,$_POST["laboratory"]);//Validar disponibilidad de consultorio/laboratorio
   
   if($repeatedLaboratory != null && $repeatedLaboratory->id != $_POST["reservationId"]){
      Core::alert("El laboratorio ya tiene un médico asignado");
      print "<script>history.back();</script>";
   }
   else{
      $reservation = ReservationData::getById($_POST["reservationId"]);
      $reservation->patient_id = $_POST["patient"];
      $reservation->medic_id = $_POST["medic"];
      $reservation->laboratory_id = $_POST["laboratory"];
      $reservation->category_id = $_POST["category"];
      $reservation->area_id = $_POST["area"];
      $reservation->date_at = $dateAt;
      $reservation->date_at_final = $_POST["date"]." ".$_POST["timeAtFinal"];
      $reservation->reason = $_POST["reason"];
      $reservation->user_id =  $_POST["userId"];
      $updatedReservation = $reservation->updatePatient();

      if($updatedReservation && $updatedReservation[0]){
         ReservationData::deleteAllProductsByReservation($_POST["reservationId"]);
         
         if(isset($_POST["products"]) && count($_POST["products"]) > 0 ){
             foreach($_POST["products"] as $product){
                 $newProduct = new ReservationData();
                 $newProduct->product_id = $product;
                 $newProduct->reservation_id = $_POST["reservationId"];
                 $addedProduct = $newProduct->addProduct();
             }
         }
     }
     else{
         Core::alert("Ocurrió un problema al guardar la cita");
     }

      print "<script>window.location='index.php?view=home';</script>";
   }
}
else Core::alert("Ingresa los datos para guardar la cita");
?>