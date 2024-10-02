<?php
if(count($_POST)>0){
  $reservation = ReservationData::getById($_POST["id"]);
  $reservation->user_id = $_POST["userId"];
  $reservation->medic_id = $_POST["medic"];
  $reservation->date_at = $_POST["date"]." ".$_POST["timeAt"];
  $reservation->date_at_final = $_POST["date"]." ".$_POST["timeAtFinal"];
  $reservation->reason = $_POST["reason"];
  $reservation->updateDoctor();
  
  print "<script>window.location='index.php?view=home';</script>";
}
?>