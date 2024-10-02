<?php
if(count($_POST)>0){
    $configuration = ConfigurationData::getAll();
    $reservation = ReservationData::getById($_POST["reservationId"]);

    //Registrar notificación en el sistema
    $notification = new NotificationData();
    $notification->patient_id = $reservation->patient_id;
    $notification->reservation_id = $reservation->id;
    $notification->type_id = 3; //Whatsapp
    $notification->direction_id = 1; //Clínica a cliente
    $notification->status_id = 1;//Enviado
    $notification->module_id = 2; //Recordatorios manuales
    $notification->receptor = $_POST["receptor"];
    $notification->message = $_POST["message"];
    $notification->add();
    return http_response_code(200);
  
}else{
  return http_response_code(500);
}
