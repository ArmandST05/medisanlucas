<?php
/*ESTUDIO DE LABORATORIO DE   */
require 'plugins/dompdf/autoload.inc.php';

//1. CARGAR FUENTES NUEVAS EN DOMPDF:
//Se utiliza el archivo ubicado en raíz load_font.php
//Desde la consola se ejecuta la sintaxis incluyendo todos los tipos de fuente (regular,bold,italic,bold-italic):
//php load_font.php "Brush Script MT" ./pathToYourFolder/BrushScriptMT-Regular.ttf ./pathToYourFolder/BrushScriptMT-Bold.ttf ./pathToYourFolder/BrushScriptMT-Italic.ttf ./pathToYourFolder/BrushScriptMT-BoldItalic.ttf
//"BrushScriptMT" = Nombre de la nueva fuente
//./pathToYourFolder/BrushScriptMT.ttf = Ubicación del archivo .ttf de la fuente (para descargar este archivo se puede buscar en https://freefontsdownload.net/)

/*//Especificar fuente en el código:
<style type="text/css">
    @font-face {
    font-family: "Brush Script MT";           
    src: local("Brush Script MT"), url("plugins/dompdf/lib/fonts/BrushScriptMT.ttf") format("truetype");
    font-weight: normal;
    font-style: normal;
}     
.italic{  
    font-family: "Brush Script MT", Brush Script MT;
    font-weight:normal;
    font-size:14pt;
} 
</style>
    */

$configuration = ConfigurationData::getAll();
$laboratoryStudy = PatientLaboratoryStudyData::getById($_GET["id"]);
$laboratoryStudyId = $_GET["id"];
$configuration = ConfigurationData::getAll();
$patient = $laboratoryStudy->getPatient();
$reservation = ReservationData::getById($laboratoryStudy->reservation_id);
$medic = $reservation->getMedic();
$studySectionsData = PatientLaboratoryStudyData::getArraySectionOptionsByPatientStudyId($laboratoryStudyId);

use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('tempDir', '/tmp');
$options->set('chroot', dirname(__FILE__, 5));

$dompdf = new Dompdf(['chroot' => dirname(__FILE__, 5)]);

$html = '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<link rel="stylesheet" type="text/css" href="storage_data/laboratory-study-1/style.css"/>
<style>
@page {
    margin: 0px !important;
}
@font-face {          
    font-family: "Calibri";                     
    src: local("Calibri"), url("plugins/dompdf/lib/fonts/Calibri.ttf") format("truetype");          
    font-weight: normal;          
    font-style: normal;      
}     
table {border-collapse: collapse;}
table td {padding: 0px} 
</style>
</head>
<body>
<img style="position:absolute;top:0.00in;left:0.00in;width:6.18in;height:10.91in" src="storage_data/laboratory-study-1/ci_1.png" />

<img style="position:absolute;top:0.00in;left:0.00in;width:8.33in;height:12in" src="storage_data/laboratory-study-1/ri_1.png" />

<img style="position:absolute;top:1.73in;left:4.54in;width:1.98in;height:0.75in" src="storage_data/laboratory-study-1/ri_2.png" />
<img style="position:absolute;top:1.73in;left:0.52in;width:1.98in;height:0.75in" src="storage_data/laboratory-study-1/ri_3.png" />
<img style="position:absolute;top:0.00in;left:0.00in;width:8.75in;height:6.06in" src="storage_data/laboratory-study-1/ri_4.png" />


<img style="position:absolute;top:8.79in;left:6.24in;width:1.23in;height:1.23in" src="[image-qr_code]" />

<img style="position:absolute;top:4.73in;left:4.56in;width:2.07in;height:0.28in" src="storage_data/laboratory-study-1/vi_2.png" />
<img style="position:absolute;top:4.73in;left:6.63in;width:1.31in;height:0.28in" src="storage_data/laboratory-study-1/vi_3.png" />
<img style="position:absolute;top:5.01in;left:4.56in;width:2.07in;height:0.27in" src="storage_data/laboratory-study-1/vi_4.png" />
<img style="position:absolute;top:5.01in;left:6.63in;width:1.31in;height:0.27in" src="storage_data/laboratory-study-1/vi_5.png" />
<img style="position:absolute;top:5.28in;left:4.56in;width:2.07in;height:0.27in" src="storage_data/laboratory-study-1/vi_6.png" />
<img style="position:absolute;top:5.28in;left:6.63in;width:1.31in;height:0.27in" src="storage_data/laboratory-study-1/vi_7.png" />
<img style="position:absolute;top:4.71in;left:6.62in;width:0.02in;height:0.86in" src="storage_data/laboratory-study-1/vi_8.png" />
<img style="position:absolute;top:5.00in;left:4.55in;width:3.41in;height:0.02in" src="storage_data/laboratory-study-1/vi_9.png" />
<img style="position:absolute;top:5.27in;left:4.55in;width:3.41in;height:0.02in" src="storage_data/laboratory-study-1/vi_10.png" />
<img style="position:absolute;top:4.71in;left:4.56in;width:0.02in;height:0.86in" src="storage_data/laboratory-study-1/vi_11.png" />
<img style="position:absolute;top:4.71in;left:7.93in;width:0.02in;height:0.86in" src="storage_data/laboratory-study-1/vi_12.png" />
<img style="position:absolute;top:4.72in;left:4.55in;width:3.41in;height:0.02in" src="storage_data/laboratory-study-1/vi_13.png" />
<img style="position:absolute;top:5.55in;left:4.55in;width:3.41in;height:0.02in" src="storage_data/laboratory-study-1/vi_14.png" />
<div style="position:absolute;top:4.73in;left:4.71in;width:1.81in;line-height:0.14in;">
<span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#ffffff">Marca de la prueba / Test brand</span>
</div>
<div style="position:absolute;top:4.74in;left:6.7in;width:1.2in;line-height:0.15in;text-align:center;">
<span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">'.$studySectionsData[3]["value"].'</span>
</div>
<div style="position:absolute;top:5.05in;left:5.50in;width:0.3in;line-height:0.11in;">
<span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#ffffff">REF</span></div>

<div style="position:absolute;top:5.05in;left:6.7in;width:1.2in;line-height:0.11in;text-align:center;">
<span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">'.$studySectionsData[4]["value"].'</span>
</div>
<div style="position:absolute;top:5.31in;left:5.49in;width:0.3in;line-height:0.11in;">
<span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#ffffff">LOT</span></div>

<div style="position:absolute;top:5.31in;left:6.7in;width:1.2in;line-height:0.11in;text-align:center;">
<span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">'.$studySectionsData[5]["value"].'</span>
</div>

<img style="position:absolute;top:4.88in;left:0.19in;width:3.28in;height:0.27in" src="storage_data/laboratory-study-1/vi_15.png" />
<img style="position:absolute;top:4.88in;left:3.48in;width:1.02in;height:0.27in" src="storage_data/laboratory-study-1/vi_16.png" />
<img style="position:absolute;top:5.15in;left:0.19in;width:3.28in;height:0.27in" src="storage_data/laboratory-study-1/vi_17.png" />
<img style="position:absolute;top:5.15in;left:3.48in;width:1.02in;height:0.27in" src="storage_data/laboratory-study-1/vi_18.png" />
<img style="position:absolute;top:4.86in;left:3.47in;width:0.02in;height:0.58in" src="storage_data/laboratory-study-1/vi_19.png" />
<img style="position:absolute;top:5.14in;left:0.18in;width:4.33in;height:0.02in" src="storage_data/laboratory-study-1/vi_20.png" />
<img style="position:absolute;top:4.86in;left:0.18in;width:0.02in;height:0.58in" src="storage_data/laboratory-study-1/vi_21.png" />
<img style="position:absolute;top:4.86in;left:4.49in;width:0.02in;height:0.58in" src="storage_data/laboratory-study-1/vi_22.png" />
<img style="position:absolute;top:4.87in;left:0.18in;width:4.33in;height:0.02in" src="storage_data/laboratory-study-1/vi_23.png" />
<img style="position:absolute;top:5.42in;left:0.18in;width:4.33in;height:0.02in" src="storage_data/laboratory-study-1/vi_24.png" />
<div style="position:absolute;top:4.92in;left:0.42in;width:2.94in;line-height:0.14in;">
<span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#ffffff">Fecha toma de muestra / Sampling date (dd/mm/yy)</span></div>
<div style="position:absolute;top:4.93in;left:3.7in;width:2.94in;line-height:0.14in;">
<span style="font-style:normal;font-weight:bold;font-size:9pt;font-family:Calibri;color:#000000">[study-date_format]</span></div>
<div style="position:absolute;top:5.19in;left:0.78in;width:2.19in;line-height:0.14in;">
<span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#ffffff">Hora toma de muestra / Sampling time</span></div>
<div style="position:absolute;top:5.21in;left:3.8in;width:2.19in;line-height:0.14in;">
<span style="font-style:normal;font-weight:bold;font-size:9pt;font-family:Calibri;color:#000000">[study-hour_format] hrs</span></div>
<img style="position:absolute;top:5.66in;left:0.18in;width:2.83in;height:0.44in" src="storage_data/laboratory-study-1/vi_25.png" />
<img style="position:absolute;top:5.66in;left:3.01in;width:2.36in;height:0.44in" src="storage_data/laboratory-study-1/vi_26.png" />
<img style="position:absolute;top:5.66in;left:5.38in;width:2.60in;height:0.44in" src="storage_data/laboratory-study-1/vi_27.png" />
<img style="position:absolute;top:6.09in;left:0.18in;width:2.83in;height:0.76in" src="storage_data/laboratory-study-1/vi_28.png" />
<img style="position:absolute;top:6.09in;left:3.01in;width:2.36in;height:0.76in" src="storage_data/laboratory-study-1/vi_29.png" />
<img style="position:absolute;top:6.09in;left:5.38in;width:2.60in;height:0.76in" src="storage_data/laboratory-study-1/vi_30.png" />
<img style="position:absolute;top:5.64in;left:3.00in;width:0.02in;height:1.23in" src="storage_data/laboratory-study-1/vi_31.png" />
<img style="position:absolute;top:5.64in;left:5.37in;width:0.02in;height:1.23in" src="storage_data/laboratory-study-1/vi_32.png" />
<img style="position:absolute;top:6.08in;left:0.16in;width:7.83in;height:0.02in" src="storage_data/laboratory-study-1/vi_33.png" />
<img style="position:absolute;top:5.64in;left:0.17in;width:0.02in;height:1.23in" src="storage_data/laboratory-study-1/vi_34.png" />
<img style="position:absolute;top:5.64in;left:7.97in;width:0.02in;height:1.23in" src="storage_data/laboratory-study-1/vi_35.png" />
<img style="position:absolute;top:5.65in;left:0.16in;width:7.83in;height:0.02in" src="storage_data/laboratory-study-1/vi_36.png" />
<img style="position:absolute;top:6.85in;left:0.16in;width:7.83in;height:0.02in" src="storage_data/laboratory-study-1/vi_37.png" />
<div style="position:absolute;top:5.74in;left:1.22in;width:0.77in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#ffffff">Prueba / Test</span><span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#ffffff"> </span><br/></SPAN></div>
<div style="position:absolute;top:5.72in;left:3.17in;width:2.08in;line-height:0.14in;"><span style="font-style:normal;font-weight:bold;font-size:9pt;font-family:Calibri;color:#ffffff">Resultado SARS-CoV-2 (COVID-19)  /</span><span style="font-style:normal;font-weight:bold;font-size:9pt;font-family:Calibri;color:#ffffff"> </span><br/></SPAN></div>
<div style="position:absolute;top:5.90in;left:3.21in;width:1.99in;line-height:0.14in;"><span style="font-style:normal;font-weight:bold;font-size:9pt;font-family:Calibri;color:#ffffff">Test result SARS-CoV-2 (COVID-19)</span><span style="font-style:normal;font-weight:bold;font-size:9pt;font-family:Calibri;color:#ffffff"> </span><br/></SPAN></div>
<div style="position:absolute;top:5.74in;left:5.62in;width:2.14in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#ffffff">Valor de Referencia / Reference Value</span><span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#ffffff"> </span><br/></SPAN></div>

<div style="position:absolute;top:6.16in;left:0.31in;width:2.66in;height:0.7in;justify-content:center;text-align:center;">
<span style="font-style:normal;font-weight:normal;font-size:10pt;font-family:Calibri;color:#000000">'.$studySectionsData[6]["value"].'</span>
</div>
<div style="position:absolute;top:5.85in;left:3.05in;width:2.27in;line-height:0.6in;text-align:center;">
<span style="font-style:normal;font-weight:bold;font-size:11pt;font-family:Calibri;color:#000000;">'.$studySectionsData[7]["value"].'</span>
</div>
<div style="position:absolute;top:6.3in;left:5.46in;width:2.61in;height:0.7in;justify-content:center;text-align: center;">
<span style="font-style:normal;font-weight:normal;font-size:10pt;font-family:Calibri;color:#000000">'.$studySectionsData[8]["value"].'</span>
</div>
<div style="position:absolute;top:0.92in;left:0.48in;width:7.28in;line-height:0.19in;"><span style="font-style:normal;font-weight:bold;font-size:12pt;font-family:Calibri;color:#000000">PRUEBA RÁPIDA PARA LA DETECCIÓN DE ANTÍGENO COVID-19 / COVID-19 RAPID ANTIGEN TEST</span><span style="font-style:normal;font-weight:bold;font-size:12pt;font-family:Calibri;color:#000000"> </span><br/><DIV style="position:relative; left:2.09in;"><span style="font-style:normal;font-weight:bold;font-size:11pt;font-family:Calibri;color:#000000">RESULTADOS DE LA PRUEBA / TEST RESULTS</span><span style="font-style:normal;font-weight:bold;font-size:11pt;font-family:Calibri;color:#000000"> </span><br/></SPAN></DIV></div>
<div style="position:absolute;top:0.53in;left:5.90in;width:0.45in;line-height:0.18in;"><span style="font-style:normal;font-weight:normal;font-size:12pt;font-family:Calibri;color:#000000">Folio:[study-id]</span></div>
<div style="position:absolute;top:2.59in;left:2.68in;width:2.85in;line-height:0.14in;"><span style="font-style:normal;font-weight:bold;font-size:9pt;font-family:Calibri;color:#000000">INFORMACIÓN CLÍNICA / CLINICAL INFORMATION</span><span style="font-style:normal;font-weight:bold;font-size:9pt;font-family:Calibri;color:#000000"> </span><br/></SPAN></div>
<div style="position:absolute;top:2.87in;left:0.60in;width:3.16in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">Motivos para realización de prueba / Reasons for testing</span><br/></SPAN></div>
<div style="position:absolute;top:2.87in;left:4.31in;width:3.44in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">Síntomas que ha presentado / Symptoms you have presented</span><br/></SPAN></div>
<div style="position:absolute;top:3.10in;left:0.44in;width:3.03in;line-height:0.14in;">
<span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">He presentado síntomas / I have presented symptoms</span><br/></SPAN></div>

<div style="position:absolute;top:3.32in;left:0.44in;width:3.75in;line-height:0.15in;">
<span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">He estado en contacto con una persona confirmada / I have been in contact with a confirmed positive person.</span>
</div>

<div style="position:absolute;top:3.65in;left:0.44in;width:3.35in;line-height:0.15in;">
<span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">Solicitud laboral- Chequeo rutnario / Professional request-Routine check.</span><br/>
</div>
<div style="position:absolute;top:4in;left:0.44in;width:3.35in;line-height:0.15in;">
<span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">Viaje / Travel</span><br/>
</div>

<div style="position:absolute;top:3.12in;left:4.29in;width:0.69in;line-height:0.14in;">
<span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">Tos / Cough</span>
</div>
<div style="position:absolute;top:3.26in;left:4.29in;width:1.88in;line-height:0.15in;">
<span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">Fiebre/ Fever</span>
</div>
<div style="position:absolute;top:3.43in;left:4.29in;width:1.88in;line-height:0.15in;">
<span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">Escurrimiento nasal / Runny nose</span>
</div>
<div style="position:absolute;top:3.60in;left:4.29in;width:1.76in;line-height:0.15in;">
<span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">Fatiga / Fatigue</span>
</div>
<div style="position:absolute;top:3.75in;left:4.29in;width:1.76in;line-height:0.15in;">
<span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">Dolor de cabeza / Headache</span>
</div>
<div style="position:absolute;top:3.90in;left:4.29in;width:1.76in;line-height:0.15in;">
<span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">Dolor de garganta / Sore throat</span>
</div>
<div style="position:absolute;top:4.08in;left:4.29in;width:2.38in;line-height:0.15in;">
<span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">Dificultad respiratoria / Difficuty breathing</span>
</div>
<div style="position:absolute;top:4.25in;left:4.29in;width:2.38in;line-height:0.15in;">
<span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">Diarrea / Diarrhea</span>
</div>
<div style="position:absolute;top:4.44in;left:4.29in;width:0.76in;line-height:0.14in;">
<span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">Otro / Other:</span>
</div>
<div style="position:absolute;top:6.95in;left:0.20in;width:1.97in;line-height:0.12in;">
<span style="font-style:normal;font-weight:bold;font-size:8pt;font-family:Calibri;color:#000000">METODOLOGÍA: Inmunocromatografía</span><span style="font-style:normal;font-weight:bold;font-size:8pt;font-family:Calibri;color:#000000"> </span><br/></SPAN></div>
<div style="position:absolute;top:7.10in;left:0.20in;width:0.30in;line-height:0.11in;"><span style="font-style:normal;font-weight:normal;font-size:7pt;font-family:Calibri;color:#000000">Notas:</span><span style="font-style:normal;font-weight:normal;font-size:7pt;font-family:Calibri;color:#000000"> </span><br/></SPAN></div>
<div style="position:absolute;top:7.23in;left:0.20in;width:7.57in;line-height:0.12in;"><span style="font-style:normal;font-weight:normal;font-size:7pt;font-family:Calibri;color:#000000">*Un resultado </span><span style="font-style:normal;font-weight:bold;font-size:7pt;font-family:Calibri;color:#000000">POSITIVO </span><span style="font-style:normal;font-weight:normal;font-size:7pt;font-family:Calibri;color:#000000">indica la presencia de SARS-CoV-2 en el momento de la toma de muestra biológica.</span><span style="font-style:normal;font-weight:normal;font-size:7pt;font-family:Calibri;color:#000000"> </span><br/></SPAN><span style="font-style:normal;font-weight:normal;font-size:7pt;font-family:Calibri;color:#000000">*Un resultado </span><span style="font-style:normal;font-weight:bold;font-size:7pt;font-family:Calibri;color:#000000">NEGATIVO </span><span style="font-style:normal;font-weight:normal;font-size:7pt;font-family:Calibri;color:#000000">no descarta la posibilidad de infección por SARS-CoV-</span><span style="font-style:normal;font-weight:normal;font-size:7pt;font-family:Calibri;color:#000000">2 </span><span style="font-style:normal;font-weight:normal;font-size:7pt;font-family:Calibri;color:#000000">debido a factores como el periodo de incubación</span><span style="font-style:normal;font-weight:normal;font-size:7pt;font-family:Calibri;color:#000000">, </span><span style="font-style:normal;font-weight:normal;font-size:7pt;font-family:Calibri;color:#000000">variabilidad biológica</span><span style="font-style:normal;font-weight:normal;font-size:7pt;font-family:Calibri;color:#000000">, </span><span style="font-style:normal;font-weight:normal;font-size:7pt;font-family:Calibri;color:#000000">calidad de la toma de</span><span style="font-style:normal;font-weight:normal;font-size:7pt;font-family:Calibri;color:#000000"> </span><br/></SPAN></div>
<div style="position:absolute;top:7.48in;left:0.20in;width:3.11in;line-height:0.11in;"><span style="font-style:normal;font-weight:normal;font-size:7pt;font-family:Calibri;color:#000000">muestra</span><span style="font-style:normal;font-weight:normal;font-size:7pt;font-family:Calibri;color:#000000">; </span><span style="font-style:normal;font-weight:normal;font-size:7pt;font-family:Calibri;color:#000000">el conjunto de estos factores es reflejado en la expresión viral.</span><span style="font-style:normal;font-weight:normal;font-size:7pt;font-family:Calibri;color:#000000"> </span><br/></SPAN></div>
<div style="position:absolute;top:7.66in;left:2.61in;width:3.00in;line-height:0.14in;"><span style="font-style:normal;font-weight:bold;font-size:9pt;font-family:Calibri;color:#000000">**</span><span style="font-style:normal;font-weight:bold;font-size:9pt;font-family:Calibri;color:#000000">Resultado exclusivo como auxiliar de diagnóstico</span><span style="font-style:italic;font-weight:bold;font-size:9pt;font-family:Calibri;color:#000000">*</span><span style="font-style:italic;font-weight:bold;font-size:9pt;font-family:Calibri;color:#000000"> </span><br/></SPAN></div>
<div style="position:absolute;top:8.12in;left:0.21in;width:4.34in;line-height:0.12in;"><span style="font-style:normal;font-weight:normal;font-size:7pt;font-family:Calibri;color:#000000">Notes:</span><span style="font-style:normal;font-weight:normal;font-size:7pt;font-family:Calibri;color:#000000"> </span><br/></SPAN><span style="font-style:normal;font-weight:normal;font-size:7pt;font-family:Calibri;color:#000000">*A </span><span style="font-style:normal;font-weight:bold;font-size:7pt;font-family:Calibri;color:#000000">POSITIVE </span><span style="font-style:normal;font-weight:normal;font-size:7pt;font-family:Calibri;color:#000000">result indicates the presence of SARS-CoV-2 at the time of biological sample collection.</span><span style="font-style:normal;font-weight:normal;font-size:7pt;font-family:Calibri;color:#000000"> </span><br/></SPAN></div>
<div style="position:absolute;top:8.37in;left:0.21in;width:7.58in;line-height:0.12in;"><span style="font-style:normal;font-weight:normal;font-size:7pt;font-family:Calibri;color:#000000">*A </span><span style="font-style:normal;font-weight:bold;font-size:7pt;font-family:Calibri;color:#000000">NEGATIVE </span><span style="font-style:normal;font-weight:normal;font-size:7pt;font-family:Calibri;color:#000000">result doesn&apos;t rule out the possibility of infection by SARS-CoV-2 due to factors such as incubation period, biological variability, quality of sample collection; all of</span><span style="font-style:normal;font-weight:normal;font-size:7pt;font-family:Calibri;color:#000000"> </span><br/><span style="font-style:normal;font-weight:normal;font-size:7pt;font-family:Calibri;color:#000000">these factors are reflected in viral expression.</span><span style="font-style:normal;font-weight:normal;font-size:7pt;font-family:Calibri;color:#000000"> </span><br/></SPAN></div>
<div style="position:absolute;top:7.98in;left:0.21in;width:2.16in;line-height:0.12in;"><span style="font-style:normal;font-weight:bold;font-size:8pt;font-family:Calibri;color:#000000">METHODOLOGY: Immunochromatography</span><span style="font-style:normal;font-weight:bold;font-size:8pt;font-family:Calibri;color:#000000"> </span><br/></SPAN></div>

<img style="position:absolute;top:10.35in;left:0.15in;width:7.86in;height:0.05in" src="storage_data/laboratory-study-1/vi_38.png" />

<img style="position:absolute;top:3.15in;left:0.28in;width:0.12in;height:0.12in" src="[image-1-1]" />
<img style="position:absolute;top:3.37in;left:0.28in;width:0.12in;height:0.12in" src="[image-1-2]" />
<img style="position:absolute;top:3.70in;left:0.28in;width:0.12in;height:0.12in" src="[image-1-3]" />
<img style="position:absolute;top:4.05in;left:0.28in;width:0.12in;height:0.12in" src="[image-1-4]" />

<img style="position:absolute;top:3.65in;left:4.13in;width:0.13in;height:0.12in" src="[image-2-5]" />
<img style="position:absolute;top:3.16in;left:4.13in;width:0.13in;height:0.12in" src="[image-2-6]" />
<img style="position:absolute;top:3.32in;left:4.13in;width:0.13in;height:0.12in" src="[image-2-7]" />
<img style="position:absolute;top:3.49in;left:4.13in;width:0.13in;height:0.12in" src="[image-2-8]" />
<img style="position:absolute;top:3.80in;left:4.13in;width:0.13in;height:0.12in" src="[image-2-9]" />
<img style="position:absolute;top:3.96in;left:4.13in;width:0.13in;height:0.12in" src="[image-2-10]" />
<img style="position:absolute;top:4.12in;left:4.13in;width:0.13in;height:0.12in" src="[image-2-11]" />
<img style="position:absolute;top:4.30in;left:4.13in;width:0.13in;height:0.12in" src="[image-2-12]" />
<img style="position:absolute;top:4.46in;left:4.13in;width:0.13in;height:0.12in" src="[image-2-13]" />

<img style="position:absolute;top:4.59in;left:5.01in;width:2.73in;height:0.04in" src="storage_data/laboratory-study-1/vi_47.png" />
<img style="position:absolute;top:0.14in;left:0.26in;width:1.12in;height:0.56in" src="storage_data/laboratory-study-1/ri_6.png" />
<div style="position:absolute;top:10.38in;left:0.37in;width:0.95in;line-height:0.14in;">
<span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">Tel. [configuration-phone]</span></div>
<div style="position:absolute;top:10.56in;left:0.37in;width:4.10in;line-height:0.14in;">
<span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">E-mail: [configuration-email]</span></div>
<img style="position:absolute;top:10.40in;left:0.17in;width:0.19in;height:0.19in" src="storage_data/laboratory-study-1/ri_7.png" />
<img style="position:absolute;top:10.60in;left:0.21in;width:0.13in;height:0.12in" src="storage_data/laboratory-study-1/ri_8.png" />
<div style="position:absolute;top:10.38in;left:5.61in;width:2.43in;line-height:0.14in;">
<span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">[configuration-address]</span></div>
<img style="position:absolute;top:10.35in;left:5.36in;width:0.28in;height:0.28in" src="storage_data/laboratory-study-1/ri_9.png" />
<img style="position:absolute;top:1.68in;left:4.08in;width:0.02in;height:0.82in" src="storage_data/laboratory-study-1/vi_63.png" />
<img style="position:absolute;top:1.69in;left:0.18in;width:7.83in;height:0.02in" src="storage_data/laboratory-study-1/vi_64.png" />
<img style="position:absolute;top:1.41in;left:0.18in;width:0.02in;height:1.09in" src="storage_data/laboratory-study-1/vi_65.png" />
<img style="position:absolute;top:1.41in;left:7.98in;width:0.02in;height:1.09in" src="storage_data/laboratory-study-1/vi_66.png" />
<img style="position:absolute;top:1.41in;left:0.18in;width:7.83in;height:0.02in" src="storage_data/laboratory-study-1/vi_67.png" />
<img style="position:absolute;top:2.48in;left:0.18in;width:7.83in;height:0.02in" src="storage_data/laboratory-study-1/vi_68.png" />
<div style="position:absolute;top:1.50in;left:3.35in;width:1.51in;line-height:0.14in;">
<span style="font-style:normal;font-weight:bold;font-size:9pt;font-family:Calibri;color:#000000">FICHA DE IDENTIFICACIÓN</span></div>
<div style="position:absolute;top:1.78in;left:0.30in;width:4in;line-height:0.16in;">
<span style="font-style:normal;font-weight:bold;font-size:10pt;font-family:Calibri;color:#000000">Nombre: [patient-name]</span></div>
<div style="position:absolute;top:1.96in;left:0.30in;width:3in;line-height:0.14in;">
<span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">Sexo: [patient-sex_name]</span></div>
<div style="position:absolute;top:2.12in;left:0.30in;width:31in;line-height:0.15in;">
<span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">Edad: [patient-age]</span>
</div>
<div style="position:absolute;top:2.25in;left:0.30in;width:31in;line-height:0.15in;">
<span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">Fecha de nacimiento: [patient-birthday_format]</span>
</div>
<div style="position:absolute;top:1.77in;left:4.20in;width:1.5in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">Teléfono [patient-phone]</span></div>
<div style="position:absolute;top:1.94in;left:4.20in;width:3in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">E-mail:[patient-email]</span></div>
<div style="position:absolute;top:2.10in;left:4.20in;width:3in;line-height:0.14in;"><span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">Número de pasaporte: [patient-passport_number]</span></div>
<img style="position:absolute;top:8.75in;left:3.2in;width:1.7in;height:0.8in" src="[medic-digital_signature]" />
<img style="position:absolute;top:9.49in;left:2.82in;width:2.4in;height:0.02in" src="storage_data/laboratory-study-1/vi_70.png" />
<div style="position:absolute;top:9.49in;left:2.96in;width:2.27in;line-height:0.21in;text-align: center;">
<span style="font-style:normal;font-weight:bold;font-size:10pt;font-family:Calibri;color:#000000">[medic-name]</span></div>
<div style="position:absolute;top:9.67in;left:2.96in;width:2.27in;line-height:0.21in;text-align: center;">
<span style="font-style:normal;font-weight:bold;font-size:10pt;font-family:Calibri;color:#000000">[medic-category_name]</span></div>
<div style="position:absolute;top:9.85in;left:2.96in;width:2.27in;line-height:0.21in;text-align: center;">
<span style="font-style:normal;font-weight:bold;font-size:10pt;font-family:Calibri;color:#000000">Céd.Prof. [medic-professional_license]</span></div>
<div style="position:absolute;top:10in;left:2.88in;width:2.43in;line-height:0.21in;text-align:center;">
<span style="font-style:normal;font-weight:bold;font-size:10pt;font-family:Calibri;color:#000000">[medic-study_center]</span></div>
</body>
</html>';

//Información básica del estudio (sección-campo)
$html = str_replace("[study-id]", str_pad($laboratoryStudyId, 4, "00", STR_PAD_LEFT), $html);
$html = str_replace("[study-date_format]", $laboratoryStudy->date_format, $html);
$html = str_replace("[study-hour_format]", $laboratoryStudy->hour_format, $html);

//Información básica del médico (sección-campo)
$html = str_replace("[medic-name]", $medic->name, $html);
$html = str_replace("[medic-category_name]", $medic->getCategory()->name, $html);
$html = str_replace("[medic-professional_license]", $medic->professional_license, $html);
$html = str_replace("[medic-study_center]", $medic->study_center, $html);

//FIRMA DIGITALIZADA
$digitalSignaturePath = "";
if ($medic->is_digital_signature == 1 && $medic->digital_signature_path) {
    $digitalSignaturePath = 'storage_data/medics/' . $medic->id . '/' . $medic->digital_signature_path;
    $html = str_replace("[medic-digital_signature]", $digitalSignaturePath, $html);
}else{
    //$digitalSignaturePath = 'assets/blank-space.png';
    $html = str_replace('<img style="position:absolute;top:8.75in;left:3.2in;width:1.7in;height:0.8in" src="[medic-digital_signature]" />', "", $html);
}



//Información básica del paciente (sección-campo)
$html = str_replace("[patient-name]", $patient->name, $html);
$html = str_replace("[patient-age]", ($patient->getAge()), $html);
$html = str_replace("[patient-birthday_format]", $patient->birthday_format, $html);
$html = str_replace("[patient-passport_number]", $patient->passport_number, $html);
$html = str_replace("[patient-email]", $patient->email, $html);
$html = str_replace("[patient-phone]", $patient->cellphone, $html);
$html = str_replace("[patient-sex_name]", ($patient->getSex()->name), $html);

//Información básica del consultorio (sección-campo)
$html = str_replace("[configuration-name]", $configuration["name"]->value, $html); //Nombre del consultorio
$html = str_replace("[configuration-phone]", $configuration["phone"]->value, $html);
$html = str_replace("[configuration-email]", $configuration["email"]->value, $html);
$html = str_replace("[configuration-address]", $configuration["address"]->value, $html);

//Marcar los checkbox de motivos y síntomas
$examCovidOptionsReasons = PatientLaboratoryStudyData::getAllOptionsByStudyIdSection($laboratoryStudyId, 1);
$examCovidOptionsSymptoms = PatientLaboratoryStudyData::getAllOptionsByStudyIdSection($laboratoryStudyId, 2);

foreach($examCovidOptionsReasons as $reason){
    if($reason->value != null){
        $imagePath = "storage_data/laboratory-study-1/checked.png";
    }else{
        $imagePath = "storage_data/laboratory-study-1/unchecked.png";
    }
    $html = str_replace("[image-1-".$reason->id."]", $imagePath, $html);
}
foreach($examCovidOptionsSymptoms as $symptom){
    if($symptom->value != null){
        $imagePath = "storage_data/laboratory-study-1/checked.png";
    }else{
        $imagePath = "storage_data/laboratory-study-1/unchecked.png";
    }
    $html = str_replace("[image-2-".$symptom->id."]", $imagePath, $html);

}

//Generar Código QR //https://github.com/chillerlan/php-qrcode
use chillerlan\QRCode\{QRCode, QROptions};
require_once 'vendor/autoload.php';

$options = new QROptions([
	'imageTransparent'  => true,
]);


$qrCodePath = 'https://'.$_SERVER["REQUEST_URI"];
$qrCode = (new QRCode($options))->render($qrCodePath);
$html = str_replace("[image-qr_code]", $qrCode, $html);

$dompdf->loadHtml($html, "UTF-8");
ob_get_clean();

$dompdf->setPaper("A24", "portrait");

$dompdf->render();
$filename = "RESULTADOS-LGRB- " . $patient->name;
// Output the generated PDF to Browser
$dompdf->stream($filename, array("Attachment" => 0));//,array("Attachment"=>0)
