<?php
require_once 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
if(count($_POST)>0){
  
    $configuration = ConfigurationData::getAll();

  //NOTIFICACIONES CORREO ELECTRÓNICO
    $mailReservation = new PHPMailer();
    $mailReservation->Host = $configuration['name']->value;
    $mailReservation->From = $configuration['email']->value;
    $mailReservation->FromName = $configuration['name']->value;
    $mailReservation->Subject = $_POST["subject"];
    $mailReservation->AddAddress($_POST["receptor"]); //Destinatarios
    $body = $_POST["message"]."<br><img src='assets/clinic-logo.png' rows='20'>";
    $mailReservation->Body = $body;
    $mailReservation->IsHTML(true);

    $reservation = ReservationData::getById($_POST["reservationId"]);

    if ($mailReservation->Send()) {
      //Registrar notificación en el sistema
      $notification = new NotificationData();
      $notification->patient_id = $reservation->patient_id;
      $notification->reservation_id = $reservation->id;
      $notification->type_id = 1; //Correo
      $notification->direction_id = 1; //Clínica a cliente
      $notification->status_id = 1;//Enviado
      $notification->module_id = 2; //Recordatorios manuales
      $notification->receptor = $_POST["receptor"];
      $notification->message = $body;
      $notification->add();
  }
  
  print "<script>window.location='index.php?view=notifications/index&sd=".$_POST["startDate"]."&ed=".$_POST["endDate"]."';</script>";
}else{
    return http_response_code(500);
    print "<script>window.location='index.php?view=notifications/index&sd=".$_POST["startDate"]."&ed=".$_POST["endDate"]."';</script>";
  }
