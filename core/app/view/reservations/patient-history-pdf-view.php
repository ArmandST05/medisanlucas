<?php

declare(strict_types=1);

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

$patient = PatientData::getById($_GET["patientId"]);
$reservations = ReservationData::getByPatient($_GET["patientId"], 0);
$configuration = ConfigurationData::getAll();

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
    .text-top{
        vertical-align: top;
    }
    .text-bottom{
        text-align: bottom;
    }
    .text-center{
        text-align: center;
    }
    td {
        font-style:normal;
        font-weight:normal;
        font-size:8pt;
        font-family: "DejaVu Sans,sans-serif";
    }
    th{
        font-style:normal;
        font-weight:bold;
        font-size:9pt;
        font-family: "DejaVu Sans";
    }
    .center-content{
        text-align: center;
    }
    table.table-content td,th{
        border-collapse: collapse;
        border-right:1px solid gray;
        border-left:1px solid gray;
        border-bottom:1px solid gray;
        border-top:1px solid gray;
    }
    </style>
</head>
<body>';
$html .= '<table width="100%">
            <tr class="border-bottom">
                <td width="100px" class="border-bottom center"><img width="100px" src="' . $logo . '" /></td>
                <td width="400px" class="border-bottom text-center"><h2>Historial citas ' . $patient->name . '</h2></td>
            </tr>
        </table>';
$html .= '<table width="100%">
        <tr>
        <td width="300px"><b>Paciente: </b> ' . $patient->name . '</td>
        <td width="300px"><b>Fecha de Nacimiento:</b> ' . $patient->birthday_format . '</td>
        </tr>
        <tr>
        <td width="300px" class="border-bottom"><b>Teléfono: </b> ' . $patient->name . '</td>
        <td width="300px" class="border-bottom"><b>Email:</b> ' . $patient->email . '</td>
        </tr>
    </table><br>
    <table width="100%" class="table-content">
<thead>
    <tr>
        <th>Fecha/Hora</th>
        <th>Médico</th>
        <th>Motivo</th>
        <th>Estatus cita</th>
        <th>Estatus pagado</th>
    </tr>
</thead>
<tbody>';
foreach ($reservations as $reservation) {
    $medic = $reservation->getMedic();

    if (isset($reservation->sale_id)) { //Existe una venta
        if ($reservation->sale_status_id == 0) { //La venta está pendiente
            $saleStatus = "PENDIENTE LIQUIDAR"; //Venta no liquidada (Pago pendiente liquidar)
        } else { //La venta está liquidada
            $saleStatus = "PAGADA"; //Venta liquidada (Pago liquidado)
        }
    } else {
        $saleStatus = "NO PAGADO";
    }

    $html .= '<tr>
            <td>' . $reservation->day_name . " " . $reservation->date_at_format . '</td>
            <td>' . $medic->name . '</td>
            <td>' . $reservation->reason . '</td>
            <td>' . $reservation->status_name . '</td>
            <td>' . ($saleStatus) . '</td>
            </tr>';
}
$html .= '</tbody>
</table><br>
    <table width="100%">
        <tr>
            <td width="300px" class="border-top border-bottom text-top">Dirección: ' . $configuration["address"]->value . '</td>
            <td class="border-top border-bottom text-top">Tel:' . $configuration["phone"]->value . '</td>
            <td class="border-top border-bottom text-top">Correo:' . $configuration["email"]->value . '</td>
        </tr>
    </table>';

$html .= '<div style="page-break-after:always"></div>';

$html .= '</body>
</html>';

$dompdf->loadHtml($html, "UTF-8");

ob_get_clean();
// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'letter');
//$dompdf->setPaper('A3', 'landscape');

// Render the HTML as PDF
$dompdf->render();

$filename = "Historial paciente " . $patient->name;
// Output the generated PDF to Browser
$dompdf->stream($filename, array("Attachment" => 0));//,array("Attachment"=>0)
