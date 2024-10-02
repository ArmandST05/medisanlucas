<?php
require 'plugins/dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;
// instantiate and use the dompdf class
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

$path = $_SERVER["DOCUMENT_ROOT"] . "/assets/suive/logosOficiales.png";
$type = pathinfo($path, PATHINFO_EXTENSION);
$data = file_get_contents($path);
$oficialLogos = 'data:image/' . $type . ';base64,' . base64_encode($data);

$suiveFormat = SuiveFormatData::getById($_GET['id']);
$startDate = $suiveFormat->start_date;
$endDate = $suiveFormat->end_date;

$diagnostics = DiagnosticData::getAllSuiveNotification();
$ageRanges = SuiveFormatData::getAllAgeRanges();

$html = '<style>
table {
    border-collapse: collapse;
    border:.3px solid black;
}
td {
    padding: 0px;
    font-style:normal;
    font-weight:normal;
    font-size:9pt;
    font-family: "Helvetica";
    border:.3px solid black;
}
th{
    background-color:#7F7D7D;
    font-style:normal;
    font-weight:bold;
    font-size:10pt;
    font-family: "Helvetica";
    color:white;
    border:.3px solid black;
}   
.group-title{
    font-weight:bold !important;
}
.verticalText {
    writing-mode: vertical-lr;
    transform: rotate(90deg);
}
.center-content{
    text-align: center;
}
.non-selected{
    background-color:#A6A6A6;
}

</style>
<body>

<div class="row">
<div class="col-6">
<img width="70%" src="' . $oficialLogos . '" />
</div>
</div>

<table width="100%">
<tr>
    <th rowspan="3"></th>
    <th rowspan="3">Diagnóstico y Código CIE10a Revisión</th>
    <th rowspan="3">EPI Clave</th>
    <th colspan="24">Número de casos según grupo de edad y sexo </th>
    <th rowspan="2" colspan="2">Total</th>
    <th rowspan="3">TOTAL</th>
</tr>
<tr>
    <th colspan="2">&lt; de 1 año</th>
    <th colspan="2">1 - 4</th>
    <th colspan="2">5 - 9</th>
    <th colspan="2">10 - 14</th>
    <th colspan="2">15 - 19</th>
    <th colspan="2">20 - 24</th>
    <th colspan="2">25 - 44</th>
    <th colspan="2">45 - 49</th>
    <th colspan="2">50 - 59</th>
    <th colspan="2">60 - 64</th>
    <th colspan="2">65 Y &gt;</th>
    <th colspan="2">Ign.</th>
</tr>
<tr>
    <th>M</th>
    <th>F</th>
    <th>M</th>
    <th>F</th>
    <th>M</th>
    <th>F</th>
    <th>M</th>
    <th>F</th>
    <th>M</th>
    <th>F</th>
    <th>M</th>
    <th>F</th>
    <th>M</th>
    <th>F</th>
    <th>M</th>
    <th>F</th>
    <th>M</th>
    <th>F</th>
    <th>M</th>
    <th>F</th>
    <th>M</th>
    <th>F</th>
    <th>M</th>
    <th>F</th>
    <th>M</th>
    <th>F</th>
</tr>';
foreach($diagnostics as $diagnotic){
$html .= '<tr>
    <td class="group-title"></td>
    <td>'.$diagnostic->name.'-'.$diagnotic->catalog_key.'</td>
    <td class="center-content">'.$diagnostic->EPI_CLAVE.'</td>';
    $totalMale = 0;
    $totalFemale= 0;
    foreach ($ageRanges as $ageRange) {
        $male = DiagnosticData::getTotalByDiagnosticIdSexRange($diagnostic->id, 1, $startDate, $endDate, $ageRange->start, $ageRange->end)->total;
        $totalMale += $male;
        $html .= '<td>' . $male . '</td>';
        $female = DiagnosticData::getTotalByDiagnosticIdSexRange($diagnostic->id, 2, $startDate, $endDate, $ageRange->start, $ageRange->end)->total;
        $totalFemale += $female;
        $html .= '<td>' . $female . '</td>';
    }
    $male = DiagnosticData::getTotalByDiagnosticIdSexNonAge($diagnostic->id, 1, $startDate, $endDate)->total;
    $totalMale += $male;
    $html .= '<td>' . $male . '</td>';
    $female = DiagnosticData::getTotalByDiagnosticIdSexNonAge($diagnostic->id, 2, $startDate, $endDate)->total;
    $totalFemale += $female;
    $html .= '<td>' . $female . '</td>';
    
    $html .='<td>' . $totalMale . '</td>
    <td>' . $totalFemale . '</td>
    <td>' . ($totalMale + $totalFemale) . '</td>
    </tr>';
}
$html .= '<br>
<tr>
<td colspan="30">
( * ) NOTIFICACIÓN INMEDIATA ( + ) HACER ESTUDIO EPIDEMIOLÓGICO ( # ) ESTUDIO DE BROTE
</td>
</tr>
<br>
<tr>
<td colspan="30">
Nota: Se debe notificar inmediatamente la presencia de brotes o epidemias de cualquier enfermedad, urgencias o emergencias epidemiológicas y desastres, así como los eventos que considere necesario incluir el Órgano Normativo.
</td>
</tr>
<tr>
<td colspan="30">
Las claves U97 a U99 son códigos provisionales utilizados por el CEMECE y la Vigilancia Epidemiológica, estas causas y sus códigos deberán ser modificados luego de los resultados de la investigación o estudio epidemiológico.
</td>
</tr>
</table>
</body>';

$dompdf->loadHtml($html);
ob_get_clean();
// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$dompdf->render();

$filename = "suive-1";
// Output the generated PDF to Browser
$dompdf->stream($filename, array("Attachment" => 0));//,array("Attachment"=>0)
