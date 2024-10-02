<?php
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

//DATOS DE RECETA PERSONALIZADA
//Las imágenes se guardan en storage_data/prescription
//El html se guarda en la tabla configuration el registro #24 "personalized_prescription_content"
//Todas las referencias de html a imágenes se deben especificar a la carpeta storage_data/prescription
//Se puede convertir el pdf que proporciona el cliente a un html en https://www.sodapdf.com/es/pdf-para-html/

$configuration = ConfigurationData::getAll();

use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('tempDir', '/tmp');
$options->set('chroot', dirname(__FILE__, 5));

$dompdf = new Dompdf(['chroot' => dirname(__FILE__, 5)]);

$html = $configuration["personalized_prescription_content"]->value;//Obtener HTML de la receta

//Datos de la cita, paciente y receta
$reservation = ReservationData::getById($_GET["id"]);
$reservationDiagnostics = DiagnosticData::getAllByReservationId($_GET["id"]);
$reservationId = $reservation->id;
$patient = $reservation->getPatient();
$medic = $reservation->getMedic();
$medicines = MedicineData::getAllByReservationId($reservationId);
$vitalSigns = ExplorationExamData::getByTypeReservation($_GET["id"], 1);
$vitalSignsArray = array_chunk($vitalSigns, 3);
$jsonPersonalizedSections = $configuration["personalized_prescription_json_sections"]->value;//Json que especifica las secciones o campos a incluir en la receta(signos vitales y sección de expediente)
//Ejemplo de Json a guardar $jsonPersonalizedSections = '{"record_section":[1],"vital_sign":[10,86,11,85,1,5,2,9]}';
$personalizedSections = json_decode($jsonPersonalizedSections, true);

//Información básica del paciente (sección-campo)
$html = str_replace("[patient-name]", $patient->name, $html);
$html = str_replace("[patient-age]", $patient->getAge(), $html);
$html = str_replace("[patient-birthday_format]", $patient->birthday_format, $html);

//Información básica del consultorio (sección-campo)
$html = str_replace("[configuration-name]", $configuration["name"]->value, $html); //Nombre del consultorio
$html = str_replace("[configuration-phone]", $configuration["phone"]->value, $html);
$html = str_replace("[configuration-email]", $configuration["email"]->value, $html);
$html = str_replace("[configuration-address]", $configuration["address"]->value, $html);

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
    $html = str_replace("[medic-digital_signature]", 'assets/blank-space.png', $html);
}

//Información básica de la cita (sección-campo)
$html = str_replace("[reservation-date_format]", $reservation->date_format, $html);

//Información básica de la cita (sección-campo)
if($reservation->observations_prescription){
$html = str_replace("[reservation-observations_prescription]", "".$reservation->observations_prescription, $html);
}else{
    $html = str_replace("[reservation-observations_prescription]", "", $html);
}


//Información básica de diagnóstico (sección-campo)
$diagnosticDetail = "";
foreach ($reservationDiagnostics as $diagnostic) {
    $diagnosticDetail .= $diagnostic->name . " " . $diagnostic->value . ",";
}
$html = str_replace("[diagnostic-details]", $diagnosticDetail, $html);
$html = str_replace("[diagnostic-diagnostic_observations]", $reservation->diagnostic_observations, $html);

//Información básica de medicamentos (sección-campo)
$medicineDetails = "";

foreach ($medicines as $index=>$medicine) {
    $medicineDetails .= ($index+1).'. '.$medicine->generic_name . "|" . $medicine->pharmaceutical_form . '|' . $medicine->concentration . '|' . $medicine->presentation . '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;>
    ' . $medicine->quantity . ' cada ' . $medicine->frequency . ' por ' . $medicine->duration . ' ' . $medicine->description.'<br>';
}
$html = str_replace("[medicine-details]", $medicineDetails, $html);

//Información de secciones de expediente (sección,número específico)
//Ejemplo de json con la información personalizada a consultar
foreach ($personalizedSections["record_section"] as $recordSection) {
    $dataRecordSection = RecordSectionData::getByRecordIdPatient($patient->id, $recordSection);
    $html = str_replace("[record_section-1]", $dataRecordSection->value, $html);
}
//Información de signos vitales personalizados (sección-campo)
foreach ($personalizedSections["vital_sign"] as $vitalSign) {
    $dataVitalSign = ExplorationExamData::getByExamIdReservation($_GET["id"], $vitalSign);
    $valueVitalSign = ($dataVitalSign) ? $dataVitalSign->value : "    ";
    $html = str_replace("[vital_sign-" . $vitalSign . "]", $valueVitalSign, $html);
}

$dompdf->loadHtml($html, "UTF-8");
ob_get_clean();
$dompdf->setPaper([0, 0, 612,  396]); //x inicio, y inicio, ancho final, alto final
//$dompdf->setPaper($configuration["personalized_prescription_paper_size"]->value, $configuration["personalized_prescription_paper_orientation"]->value);

$dompdf->render();
$filename = "Receta " . $patient->name;
// Output the generated PDF to Browser
$dompdf->stream($filename, array("Attachment" => 0));//,array("Attachment"=>0)