<?php
    $reservation = new ReservationData();
    $reservation->user_id =  $_POST["userId"];
    $reservation->medic_id = $_POST["medic"];
    $reservation->date_at = $_POST["date"]." ".$_POST["timeAt"];
    $reservation->date_at_final = $_POST["date"]." ".$_POST["timeAtFinal"];
    $reservation->reason = $_POST["reason"];

    $reservation->addDoctor();
    Core::redir("./index.php?view=home&date=".$_POST["date"]."");
?>