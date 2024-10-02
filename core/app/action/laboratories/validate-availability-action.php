<?php
    $dateAt = $_POST["date"]." ".$_POST["timeAt"].":01";
    $dateAtFinalData = $_POST["date"]." ".$_POST["timeAtFinal"].":00";
    //A la fecha final se le quitará un segundo para evitar conflicto en la consulta con cita siguiente
    $dateAtFinal = date("Y-m-d H:i:s",strtotime($dateAtFinalData." -1 seconds"));
    $reservationId = $_POST["reservationId"];

    $laboratories = LaboratoryData::getAvailableByDate($dateAt,$dateAtFinal,$reservationId);  

    echo json_encode($laboratories);
  
?>