<?php
if(count($_POST) > 0){

    $dateAt = $_POST["date"]." ".$_POST["timeAt"];
    $user = UserData::getLoggedIn();
    $medic = MedicData::getByUserId($user->id);

    $reservation = new ReservationData();
    $reservation->patient_id = $_POST["patient"];
    $reservation->medic_id = $medic->id;//Médico que inició sesión
    $reservation->laboratory_id = $_POST["laboratory_id"];
    $reservation->category_id = $_POST["category_id"];
    $reservation->area_id = $_POST["area_id"];
    $reservation->date_at = $dateAt;
    $reservation->date_at_final = $_POST["date"]." ".$_POST["timeAtFinal"];
    $reservation->reason = null;
    $reservation->user_id = $user->id;
    $addedReservation = $reservation->addByPatient();

    if($addedReservation && $addedReservation[0]){
        $newReservationId = $addedReservation[1];
        //Iniciar consulta
        $reservation = ReservationData::getById($newReservationId);
        $reservation->status_id = 2;
        $reservation->updateStatus();

       echo $addedReservation[1];
    }
    else{
        return http_response_code(500);
    }
}else{
    return http_response_code(500);
}
?>