<?php
/**
* BookMedik
* @author evilnapsis
**/
$diagnostic = new DiagnosticData();
if($diagnostic->deleteByReservation($_POST["reservationDiagnosticId"])) return $diagnostic;
else return http_response_code(500);

?>