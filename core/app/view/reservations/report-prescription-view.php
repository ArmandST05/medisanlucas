<?php
declare(strict_types=1); //SATA FIEL

require 'plugins/dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;
// instantiate and use the dompdf class
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

//Logo de clínica
$logo = "";
$path = $_SERVER["DOCUMENT_ROOT"] . "/assets/clinic-logo.png";
//Validar que se ha subido un logo de la clínica para mostrar
if (file_exists($path)) {
    $type = pathinfo($path, PATHINFO_EXTENSION);
    $data = file_get_contents($path);
    $logo = 'data:image/' . $type . ';base64,' . base64_encode($data);
}

$reservation = ReservationData::getById($_GET["id"]);
$reservationDiagnostics = DiagnosticData::getAllByReservationId($_GET["id"]);
$configuration = ConfigurationData::getAll();
$reservationId = $reservation->id;
$patient = $reservation->getPatient();
$medic = $reservation->getMedic();
$medicines = MedicineData::getAllByReservationId($reservationId);
$vitalSigns = ExplorationExamData::getByTypeReservation($_GET["id"], 1);
$vitalSignsArray = array_chunk($vitalSigns, 3);

//Ya que hay medicamentos que requieren receta individuales, en la vista se registra el número de receta en la que va el medicamento
//Y en el pdf se generan las páginas de acuerdo al número 
$prescriptions = [];
if ($medicines) {
    foreach ($medicines as $medicine) {
        $prescriptions[$medicine->prescription_number]["medicines"][] = $medicine;
    }
} else $prescriptions[1]["medicines"] = [];
ksort($prescriptions);

//LOGO DE CENTRO DE ESTUDIOS
$studyCenterLogo = "";
if ($medic->is_study_center_prescription == 1 && $medic->study_center_logo) {
    $studyCenterLogoPath = 'storage_data/medics/' . $medic->id . '/' . $medic->study_center_logo;
    //Validar que se ha subido un logo de la clínica para mostrar
    if (file_exists($studyCenterLogoPath)) {
        $type = pathinfo($studyCenterLogoPath, PATHINFO_EXTENSION);
        $data = file_get_contents($studyCenterLogoPath);
        $studyCenterLogo = 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
}


//FIRMA DIGITALIZADA
$digitalSignature = "";
if ($medic->is_digital_signature == 1 && $medic->digital_signature_path) {
    $digitalSignaturePath = 'storage_data/medics/' . $medic->id . '/' . $medic->digital_signature_path;
    //Validar que se ha subido un logo de la clínica para mostrar
    if (file_exists($digitalSignaturePath)) {
        $type = pathinfo($digitalSignaturePath, PATHINFO_EXTENSION);
        $data = file_get_contents($digitalSignaturePath);
        $digitalSignature = 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
}

//PRIVATE KEY (.key)FIEL SAT
$privateKeyString = "";
$sourceString = "";
try {
    if ($medic->is_file_key == 1 && $medic->fiel_key_path && $medic->fiel_key_password) {
        $pemKeyFile = 'storage_data/medics/' . $medic->id . '/' . $medic->fiel_key_path;
        //Validar si el archivo existe
        if (file_exists($pemKeyFile)) {
            $passPhrase = $medic->fiel_key_password; // contraseña para abrir la llave privada

            $key = PhpCfdi\Credentials\PrivateKey::openFile($pemKeyFile, $passPhrase);

            $sourceString = "|" . $reservation->date_at . "|" . $reservation->id . "|" . $medic->name . "|" . $configuration['name']->value . "|" . $medic->getCategory()->name . "|" . $medic->professional_license . "|" . $configuration['phone']->value . "|" . $configuration['address']->value . "|" . $patient->name;

            // alias de privateKey/sign/verify
            $signature = $key->sign($sourceString);
            $privateKeyString = base64_encode($signature);
            echo base64_encode($signature), PHP_EOL;
        }
    }
} catch (Exception $e) {
}

$html = '<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style>
    table {
        border-collapse: collapse;
    }
    .border-bottom{
        border-bottom:3px solid #52BCE4;
    }
    .border-top{
        border-top:3px solid #52BCE4;
    }

    .underline{
        border-bottom:1px solid #52BCE4;
    }
    .text-bottom{
        text-align: bottom;
    }
    .text-center{
        text-align: center;
    }
    .subtitle{
        font-size:12pt;
    }
    .medicine-concentration{
        font-size:7pt;
    }
    .key{
        font-size:6pt;
    }
    td, p {
        font-style:normal;
        font-weight:normal;
        font-size:9pt;
        font-family: "DejaVu Sans,sans-serif";
    }
    th{
        font-style:normal;
        font-weight:bold;
        font-size:10pt;
        font-family: "DejaVu Sans";
    }
    .center-content{
        text-align: center;
    }
    </style>
</head>
<body>';
foreach ($prescriptions as $prescription) {
    $html .= '<table width="100%">
    <tr class="border-bottom">
    <td width="100px" class="border-bottom center"><img width="100px" src="' . $logo . '" /></td>
    <td width="400px" class="border-bottom text-center">' . $configuration["name"]->value . '<br><b class="subtitle">' . $medic->name . '</b>
    <br>' . $medic->getCategory()->name . '<br>' . $medic->study_center . '<br>Cédula:' . $medic->professional_license . '<br>';
    if ($medic->other_specialties) {
        $html .= '<span style="font-size:7pt;" class="text-center">' . $medic->other_specialties . '</span>';
    }
    $html .= '</td>';
    if ($medic->is_study_center_prescription == 1 && $medic->study_center_logo) {
        $html .= '<td width="90px" class="border-bottom center"><img width="90px" src="' . $studyCenterLogo . '" /></td>';
    }
    $html .= '</tr>';

    $html .= '</table>
    <table width="100%">
    <tr>
    <td width="500px"><b>Paciente: </b>' . $patient->name . '</td>
    <td><b>Folio: </b>' . str_pad($reservation->id, 5, "0", STR_PAD_LEFT) . '</td>
    </tr>
    <tr>
    <td width="500px" class="border-bottom"><b>Fecha de Nacimiento:</b>' . $patient->birthday_format . '</td>
    <td class="border-bottom"><b>Fecha:</b>' . $reservation->date_format . '</td>
    </tr>
    </table>
    <table width="100%">
    <tr>
    <td width="500px"><b>Signos vitales: </b></td></tr>
    </table>';
    $html .= '<table width="100%">';
    foreach ($vitalSignsArray as $vitalSigns) {
        $html .= '<tr>';
        foreach ($vitalSigns as $vitalSign) {
            $html .= '<td>' . $vitalSign->name . '</td>
            <td class="underline">' . $vitalSign->value . '</td>';
        }
        $html .= '</tr>';
    }
    $html .= '</table>
    <br>' . $reservation->diagnostic_observations . '
    </tr>
    </table>
    <table width="100%">
    <tr>
    <td width="500px" class="border-bottom"><b>Diagnóstico: </b>';
    foreach ($reservationDiagnostics as $diagnostic) {
        $html .= $diagnostic->name . " " . $diagnostic->value . ",";
    }
    $html .= '
    <br>' . $reservation->diagnostic_observations . '
    </tr>
    </table>
    <table width="100%">';
    foreach ($prescription["medicines"] as $medicine) {
        $html .= '<tr>
    <td width="250px"><b>' . $medicine->generic_name . " | " . $medicine->pharmaceutical_form . '</b><label class="medicine-concentration"><br>' . $medicine->concentration . '<br>' . $medicine->presentation . '</label></td>
    <td>' . $medicine->quantity . ' cada ' . $medicine->frequency . ' por ' . $medicine->duration . '</td>
    <td>' . $medicine->description . '</td>
    </tr>';
    }
    $html .= '<p>'.$reservation -> observations_prescription .'</p><br><br>';
    $html .= '</table><br><br>';

    $html .= '<table width="50%" style="margin:0 auto;">
        <tr>
            <td class="center-content">';
    if ($digitalSignature != "") {
        $html .= '<img width="100px" src="' . $digitalSignature . '" />';
    }
    $html .= '</td>
        </tr>
        <tr>
            <td class="center-content">________________________________</td>
        </tr>
        <tr>
            <td class="center-content">' . $medic->name . '</td>
        </tr>
        </table><br>
        <table width="100%">
            <tr>
                <td width="300px" class="border-top border-bottom">' . $configuration["address"]->value . '</td>
                <td class="border-top border-bottom">Tel:' . $configuration["phone"]->value . '</td>
                <td class="border-top border-bottom">Correo:' . $configuration["email"]->value . '</td>
            </tr>
        </table>';
    if ($privateKeyString != "") {
        $html .= '<table width="100%">
            <tr><td class="key">Cadena original</td></tr>
                <tr>
                    <td width="100px" class="key">' . $sourceString . '</td>
                </tr>
                <tr><td class="key">Sello Digital</td></tr>
                <tr>
                    <td style="width:200px;height:auto"; class="key"><div style="word-wrap:break-word;border: dashed 1px gray;"> ' . $privateKeyString . '</div></td>
                </tr>
            </table>';
    }
    $html .= '<div style="page-break-after:always"></div>';
}

$html .= '</body>
</html>';

$dompdf->loadHtml($html, "UTF-8");
ob_get_clean();
// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'letter');
//$dompdf->setPaper('A3', 'landscape');

// Render the HTML as PDF
$dompdf->render();

$filename = "Receta " . $patient->name;
// Output the generated PDF to Browser
$dompdf->stream($filename, array("Attachment" => 0));//,array("Attachment"=>0)