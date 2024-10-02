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
$configuration = ConfigurationData::getAll();
$reservationId = $reservation->id;
$patient = $reservation->getPatient();
$medic = $reservation->getMedic();
$medicines = MedicineData::getAllByReservationId($reservationId);
$vitalSigns = ExplorationExamData::getByTypeReservation($_GET["id"], 1);
$vitalSignsArray = array_chunk($vitalSigns, 2);

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

$patientImage = ($patient->image) ? "storage_data/patients/" . $patient->image : "../../../assets/default_user.jpg";

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
    .text-bottom{
        text-align: bottom;
    }
    .text-center{
        text-align: center;
    }
    .title-document{
        font-size:13pt;
        font-weight:bold;
        text-align: center;
    }
    .patient-title{
        font-size:8pt;
        font-weight:bold;
        text-align: justify;
    }
    .subtitle{
        font-size:11pt;
        border-bottom:3px solid #52BCE4;
    }
    .medicine-concentration{
        font-size:7pt;
    }
    .key{
        font-size:6pt;
    }
    td {
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
<body>
<table width="100%">
<tr>
<td width="150px" class="border-bottom center"><img width="100px" src="' . $logo . '" /></td>

<td width="400px" class="title-document border-bottom">' . $configuration["name"]->value . '<br>HISTORIA CLÍNICA</td>
</tr>
</table>
<table width="100%">
<tr>
    <td colspan="5" class="subtitle">Paciente: ' . $patient->name . '</td>
</tr>
<tr>
    <td rowspan="8"><img width="100px" src="' . $patientImage . '" /></td>
    <td class="patient-title">Sexo:</td>
    <td class="patient-content" colspan="3">' . $patient->getSex()->name . '</td>
</tr>
<tr>
    <td class="patient-title">CURP:</td>
    <td class="patient-content" colspan="3">' . $patient->curp . '</td>
</tr>
<tr>
    <td class="patient-title">Fecha de nacimiento:</td>
    <td class="patient-content">' . $patient->getBirthdayFormat() . '</td>
    <td class="patient-title">Edad:</td>
    <td class="patient-content">' . $patient->getAge() . '</td>
</tr>
<tr>
    <td class="patient-title">Dirección:</td>
    <td class="patient-content" colspan="3">' . $patient->street . ' ' . $patient->number . ' ' . $patient->getCounty()->name . '</td>
</tr>
<tr>
    <td class="patient-title">Teléfono:</td>
    <td class="patient-content">' . $patient->cellphone . '</td>
    <td class="patient-title">Teléfono alternativo:</td>
    <td class="patient-content">' . $patient->homephone . '</td>
</tr>

<tr>
    <td class="patient-title">Correo electrónico:</td>
    <td class="patient-content" colspan="3">' . $patient->email . '</td>
</tr>
<tr>
<td class="patient-title">Nombre de familiar:</td>
<td class="patient-content" colspan="3">' . $patient->relative_name . '</td>
</tr>
<tr>
<td class="patient-title">Referido:</td>
<td class="patient-content" colspan="2">' . $patient->referred_by . '</td>
</tr>
</table>

<table width="100%">';
foreach ($medicines as $medicine) {
    $html .= '<tr>
<td width="250px"><b>' . $medicine->generic_name . " | " . $medicine->pharmaceutical_form . '</b><label class="medicine-concentration"><br>' . $medicine->concentration . '<br>' . $medicine->presentation . '</label></td>
<td>' . $medicine->quantity . ' cada ' . $medicine->frequency . ' por ' . $medicine->duration . '</td>
<td>' . $medicine->description . '</td>
</tr>';
}
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

$html .= '</body>
</html>';

$dompdf->loadHtml($html, "UTF-8");
ob_get_clean();
// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'letter');
//$dompdf->setPaper('A3', 'landscape');

// Render the HTML as PDF
$dompdf->render();

$filename = "suive-1";
// Output the generated PDF to Browser
$dompdf->stream($filename, array("Attachment" => 0));//,array("Attachment"=>0)
