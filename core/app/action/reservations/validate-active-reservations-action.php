<?php
    $dateAt = $_POST["date"]." ".$_POST["timeAt"].":01";
    $dateAtFinalData = $_POST["date"]." ".$_POST["timeAtFinal"].":00";
    //A la fecha final se le quitará un segundo para evitar conflicto en la consulta con cita siguiente
    $dateAtFinal = date("Y-m-d H:i:s",strtotime($dateAtFinalData." -1 seconds"));

    $user = UserData::getLoggedIn();
    $medic = MedicData::getByUserId($user->id);

    $reservations = ReservationData::getActiveReservationsByMedicDate($dateAt,$dateAtFinal,$medic->id);  

    echo json_encode($reservations);
  
?>