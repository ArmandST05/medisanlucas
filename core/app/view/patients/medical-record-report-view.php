<?php
require_once __DIR__ . '/../../../../vendor/autoload.php';
try {
    $mpdf = new \Mpdf\Mpdf(['format' => 'Letter',]);

    //Logo de clínica
    $logo = "";
    $path = $_SERVER["DOCUMENT_ROOT"] . "/assets/clinic-logo.png";
    //Validar que se ha subido un logo de la clínica para mostrar
    if (file_exists($path)) {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $logo = 'data:image/' . $type . ';base64,' . base64_encode($data);
    }

    //DATOS DEL PACIENTE
    $user = UserData::getLoggedIn();
    $userType = $user->user_type;

    $patient = PatientData::getById($_GET["id"]);
    $patientId = $_GET["id"];

    $recordSections = RecordSectionData::getRecordsByPatient($patientId);
    $recordSectionsArray = array_chunk($recordSections, 2);
    $reservationsHistory = ReservationData::getByPatient($patientId, 2);
    $totResHistory = count($reservationsHistory);
    
    $html = '
    <style>
    table.patient-details tr,
    table.patient-details th,
    table.patient-details td{
        border: none;
        font-family: "Helvetica";
        font-size:9pt;
        text-align:left;
    }
    td {
        padding: 0px;
        font-style:normal;
        font-weight:normal;
        font-family: "Helvetica";
    }
    .title-document{
        font-size:12pt;
        font-weight:bold;
        font-family: "Helvetica";
        text-align:center;
    }
    .title{
        background-color:#D6DBDF;
        font-style:normal;
        font-weight:bold;
        font-size:10pt;
    }     
    .subtitle-document{
        border-bottom: 2px solid rgb(13,173,224);
        text-align: left;
        font-size:12pt;
        font-weight:bold;
        font-family: "Helvetica";
    }
    .subtitle-reservation{
        border-bottom: 2px solid rgb(13,173,224);
        text-align: left;
        font-size:10pt;
        font-weight:bold;
        font-family: "Helvetica";
    }
    .table-title{
        //background-color:#E8E8E8;
        color:white;
        font-style:normal;
        font-weight:bold;
        font-size:10pt;
    }
    .group-title{
        font-weight:bold !important;
    }
    .center{
        text-align: center;
    }
    </style>
    <table width="100%">
        <tr>
            <td width="150px"><img width="150px" height="90px" src="' . $logo . '" /></td>
            <td class="title-document">EXPEDIENTE DEL PACIENTE <br>' . $patient->name . '</td>
        </tr>
    </table>
    <br>
    <table width="100%">
        <tr>
            <td class="subtitle-document bold">DATOS GENERALES</td>
        </tr>
    </table>
    <br>
    <table width="100%" class="patient-details">
        <tr>
            <td><b>Nombre: </b>' . $patient->name . '<br>
            <b>Edad: </b>' . $patient->getAge() . '<br>
            </td>
        </tr>
    </table>
    </br>
    <table width="100%">
    <tr>
        <td class="subtitle-reservation bold" colspan="2">ANTECEDENTES</td>
    </tr></table>
    <table width="100%" class="patient-details">';
    foreach ($recordSectionsArray as $recordSections) {
        $html .= '<tr class="title">';
        foreach ($recordSections as $recordSection) {
            $html .= '<td><b>' . $recordSection->name . '</b></td>';
        }
        $html .= '/<tr>';
        $html .= '<tr class="patient-details">';
        foreach ($recordSections as $recordSection) {
            $html .= '<td>' . $recordSection->value . '<br></td>';
        }
    }
    $html .= '</table><br>
<table width="100%">
<tr>
    <td class="subtitle-reservation bold">OBSERVACIONES</td>
</tr>
<tr>
    <td>' . $patient->notes . '</td>
</tr>
</table>
<div style="page-break-after: always"></div>';
    foreach ($reservationsHistory as $reservationData) {
        $reservationId = $reservationData->id;
        $months = ["01" => "Enero", "02" => "Febrero", "03" => "Marzo", "04" => "Abril", "05" => "Mayo", "06" => "Junio", "07" => "Julio", "08" => "Agosto", "09" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre"];
        $reservation = ReservationData::getById($reservationData->id);
        $medic = $reservation->getMedic();
        $reservationDateFormat = substr($reservation->date_at, 8, 2) . "/" . $months[substr($reservation->date_at, 5, 2)] . "/" . substr($reservation->date_at, 0, 4);

        $vitalSigns = ExplorationExamData::getByTypeReservation($reservationId, 1);
        $vitalSignsArray = array_chunk($vitalSigns, 2);
        $physicalExams = ExplorationExamData::getByTypeReservation($reservationId, 2);
        $physicalExamsArray = array_chunk($physicalExams, 2);
        $topographicalExams = ExplorationExamData::getByTypeReservation($reservationId, 3);
        $topographicalExamsArray = array_chunk($topographicalExams, 2);
        $reservationDiagnostics = DiagnosticData::getAllByReservationId($reservationId);
        $reservationMedicines = MedicineData::getAllByReservationId($reservationId);
    
        $html .= '<table width="100%">
    <tr>
        <td class="subtitle-document bold">CONSULTA ' . ($reservationDateFormat) . '</td>
    </tr>
    </table>
    <table width="100%" class="patient-details">
        <tr>
            <td class="title"><b>Fecha: </b></td>
            <td>' . ($reservationDateFormat) . '</td>
            <td class="title"><b>Hora: </b></td>
            <td>' . $reservation->getStartTime() . " - " . $reservation->getEndTime() . '</td>
            <td class="title"><b>Doctor: </b></td>
            <td>' . $reservation->getMedic()->name . '</td>
            <td class="title"><b>Agendado por: </b></td>
            <td>' . $reservation->getMedic()->name . '</td>
        </tr>
        <tr>
            <td class="title"><b>Área: </b></td>
            <td>' . $reservation->getArea()->name . '</td>
            <td class="title"><b>Categoría: </b></td>
            <td>' . $reservation->getCategory()->name . '</td>
            <td class="title"><b>Laboratorio/Consultorio: </b></td>
            <td>' . $reservation->getLaboratory()->name . '</td>
            <td class="title"><b>Estatus: </b></td>
            <td>ASISTIÓ PACIENTE</td>
        </tr>
    </table>
    <table width="100%">
        <tr>
            <td class="subtitle-reservation bold">MOTIVO DE CONSULTA</td>
        </tr>
    </table>
    <table width="100%" class="patient-details">
        <tr>
            <td>' . $reservation->reason . '<br></td>
        </tr>
    </table>
    <br>
    <table width="100%">
        <tr>
            <td class="subtitle-reservation bold">(S) SUBJETIVO - OBSERVACIONES DEL PACIENTE</td>
        </tr>
    </table>
    <table width="100%" class="patient-details">
        <tr>
            <td>' . $reservation->patient_observations . '<br></td>
        </tr>
    </table>
    <table width="100%">
        <tr>
            <td class="subtitle-reservation bold">(O) OBJETIVO - SIGNOS VITALES</td>
        </tr>
    </table>
    <table width="100%" class="patient-details">';
    foreach ($vitalSignsArray as $vitalSigns){
        $html.='<tr>';
        foreach ($vitalSigns as $vitalSign){
            $html.='<td class="title"><b>'.$vitalSign->name.'</b></td>
            <td>' . $vitalSign->value . '</td>';
        }
        $html.='</tr>';
    }
    $html.='</table>
    <br>
    <table width="100%">
        <tr>
            <td class="subtitle-reservation bold">(O) OBJETIVO - EXAMEN FÍSICO</td>
        </tr>
    </table>
    <table width="100%" class="patient-details">';
    foreach ($physicalExamsArray as $physicalExams){
        $html.='<tr>';
        foreach ($physicalExams as $physicalExam){
            $html.='<td class="title"><b>'.$physicalExam->name.'</b></td>
            <td>' . $physicalExam->value . '</td>';
        }
        $html.='</tr>';
    }
    $html.='</table>
    <br>
    <table width="100%" class="patient-details">
        <tr>
            <td>' . $reservation->physical_observations . '<br></td>
        </tr>
    </table>
    <br>
    <table width="100%">
        <tr>
            <td class="subtitle-reservation bold">(O) OBJETIVO - EXPLORACIÓN TOPOGRÁFICA</td>
        </tr>
    </table>
    <table width="100%" class="patient-details">
        <tr>
            <td>' . $reservation->topographical_observations . '<br></td>
        </tr>
    </table>
    <table width="100%" class="patient-details">';
    foreach ($topographicalExamsArray as $topographicalExams){
        $html.='<tr>';
        foreach ($topographicalExams as $topographicalExam){
            $html.='<td class="title"><b><br>'.$topographicalExam->name.'</b></td>
            <td>' . $topographicalExam->value . '</td>';
        }
        $html.='</tr>';
    }
    $html.='</table>
    <br>
    <table width="100%" class="patient-details">
        <tr>
            <td>' . $reservation->topographical_observations . '<br></td>
        </tr>
    </table>
    <br>
    <table width="100%">
        <tr>
            <td class="subtitle-reservation bold">(O) ANÁLISIS - DIAGNÓSTICOS</td>
        </tr>
    </table>
    <table width="100%" class="patient-details">
        <tr>
            <td>' . $reservation->diagnostic_observations . '<br></td>
        </tr>
    </table>
    <table width="100%" class="patient-details">';
    foreach ($reservationDiagnostics as $diagnostic){
        $html.='<tr>
                <td>'.$diagnostic->catalog_key.'</td>
                <td>' . $diagnostic->name . '</td>
                <td>' . $diagnostic->value . '</td>
            </tr>';
    }
    $html.='</table>
    <table width="100%">
        <tr>
            <td class="subtitle-reservation bold">(P) PLAN - TRATATAMIENTO O PLAN</td>
        </tr>
    </table>
    <table width="100%" class="patient-details">
        <tr>
            <td>' . $reservation->treatment_observations . '<br></td>
        </tr>
    </table><br>
    <table width="100%">
        <tr>
            <td class="subtitle-reservation bold">(P) PLAN - RECETA DE MEDICAMENTOS</td>
        </tr>
    </table>
    <table width="100%" class="patient-details">
        <tr>
            <td><b>Medicamento</b></td>
            <td><b>Tomar</b></td>
            <td><b>Frecuencia</b></td>
            <td><b>Duración</b></td>
            <td><b>Notas</b></td>
        </tr>';
    foreach ($reservationMedicines as $medicine){
        $html.='<tr>
                <td><b>'.$medicine->generic_name . "|" . $medicine->pharmaceutical_form.'</b><br>'.$medicine->concentration . '<br>' . $medicine->presentation.'</td>
                <td>' . $medicine->quantity. '</td>
                <td>' . $medicine->frequency . '</td>
                <td>' . $medicine->duration . '</td>
                <td>' . $medicine->description . '</td>
            </tr>';
    }
    $html.='</table>
    <div style="page-break-after: always"></div>';
    }
    $html .= '';
    $mpdf->WriteHTML($html);

    // Other code
    ob_get_clean();
    $mpdf->Output();
} catch (\Mpdf\MpdfException $e) { // Note: safer fully qualified exception name used for catch
    // Process the exception, log, print etc.
    echo $e->getMessage();
}
