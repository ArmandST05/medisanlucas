<?php
$dateAt = $_POST["date"]." ".$_POST["timeAt"];
$repeatedReservation = ReservationData::getRepeatedReservation($_POST["patient"],$_POST["medic"],$_POST["date"]." ".$_POST["timeAt"],$_POST["laboratory"]);//Validar cita repetida
$repeatedLaboratory = ReservationData::getRepeatedLaboratory($dateAt,$_POST["laboratory"]);//Validar disponibilidad de consultorio/laboratorio
$patientValidate = PatientData::getValidatePatientCategory($_POST["patient"],3);//Validar si el paciente no está en lista negra.

if($patientValidate != null){
    Core::alert("El paciente está en lista negra");
}
else if($repeatedLaboratory != null){
    Core::alert("El laboratorio ya tiene un médico asignado");
}
else if($repeatedReservation != null){
    Core::alert("La cita está repetida");
}
else{
    $reservation = new ReservationData();
    $reservation->patient_id = $_POST["patient"];
    $reservation->medic_id = $_POST["medic"];
    $reservation->laboratory_id = $_POST["laboratory"];
    $reservation->category_id = $_POST["category"];
    $reservation->area_id = $_POST["area"];
    $reservation->date_at = $dateAt;
    $reservation->date_at_final = $_POST["date"]." ".$_POST["timeAtFinal"];
    $reservation->reason = $_POST["reason"];
    $reservation->user_id =  $_POST["userId"];
    $addedReservation = $reservation->addByPatient();

    if($addedReservation && $addedReservation[0]){
        if(isset($_POST["products"]) && count($_POST["products"]) > 0){
            foreach($_POST["products"] as $product){
                $newProduct = new ReservationData();
                $newProduct->product_id = $product;
                $newProduct->reservation_id = $addedReservation[1];
                $addedProduct = $newProduct->addProduct();
            }
        }
    }
    else{
        Core::alert("Ocurrió un problema al guardar la cita");
    }
}

Core::redir("./index.php?view=home&date=". $_POST["date"]."");
?>