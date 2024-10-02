<?php
use PhpOffice\PhpWord\Autoloader;
use PhpOffice\PhpWord\Settings;

$word = new  PhpOffice\PhpWord\PhpWord();
$totalPay=0;
$sale = OperationData::getById($_GET["id"]);
$operations = OperationDetailData::getAllProductsByOperationId($_GET["id"]);
$payments = OperationPaymentData::getAllByOperationId($_GET["id"]);
foreach($payments as $pay1){
    $totalPay+=$pay1->total;
}

$date= $sale->created_at;

if($sale->patient_id != null){ $client = PatientData::getById($sale->patient_id);}
$user = $sale->getUser();

$section1 = $word->AddSection();
if(file_exists("../../../../assets/clinic-logo.png"))
$section1->addImage(
    "../../../../assets/clinic-logo.png",
    array(
        'width' => 160,
        'height' => 75,
        'wrappingStyle' => 'behind'
    )
);

$styleTable = array('name' => 'Cambria','font-color'=> '404040','border'=>0,'borderColor' => '888888', 'cellMargin' => 40,"color"=>"404040");
$styleFirstRow = array('name' => 'Cambria','borderBottomColor' => '0000FF', 'bgColor' => '31AFDF',"width"=>"0.9","high"=>"0.9","color"=>"404040");
$styleTable1 = array('name' => 'Cambria','font-color'=> '404040','border'=>0,'borderColor' => 'C5B9BC', 'cellMargin' => 50,"color"=>"404040", 'borderSize' => 6);

$total=0;

$table1 = $section1->addTable("table1");
$table1->addRow();
$table1->addCell(3000)->addText(("FOLIO VENTA: ".$_GET["id"].""),array("size"=>14,"color"=>"FFFFFF", "bold" => true));
$table1->addCell(6000)->addText(("                         "."            ".$date),array("size"=>14,"color"=>"FFFFFF", "bold" => true));

$table1->addRow();
$table1->addCell()->addText(("Datos del Paciente"),array("size"=>12,"color"=>"404040", "bold"  => true));
if($sale->patient_id!=null){
$table1->addRow();
$table1->addCell()->addText("Nombre:");
$table1->addCell()->addText($client->name);
$table1->addRow();
$table1->addCell()->addText("Dirección:");
$table1->addCell()->addText($client->calle." ".$client->num." ".$client->col." ");
$table1->addRow();
$table1->addCell()->addText("Teléfono:");
$table1->addCell()->addText($client->tel);
}
$section1->addText("");

$section1->addText(("Detalle venta"),array("size"=>12,"color"=>"404040", "bold"  => true));;

$table2 = $section1->addTable("table2");
$table2->addRow();

$table2->addCell(3000)->addText(("Nombre del producto"),array("size"=>12,"color"=>"FFFFFF","bold" => true));
$table2->addCell(1000)->addText(("Cantidad"),array("size"=>12,"color"=>"FFFFFF", "bold"  => true));
$table2->addCell(2500)->addText(("P.U"),array("size"=>12,"color"=>"FFFFFF", "bold"  => true));
$table2->addCell(2500)->addText(("Total"),array("size"=>12,"color"=>"FFFFFF", "bold"  => true));

foreach($operations as $operation){
	$product = $operation->getProduct();
	$table2->addRow();
    $table2->addCell()->addText($product->name);
    $table2->addCell()->addText($operation->quantity);
    $table2->addCell()->addText("$".number_format($operation->price,2,".",","));
    $table2->addCell()->addText("$".number_format($operation->quantity*$operation->price,2,".",","));
    $total += $operation->quantity * $operation->price;
}

$table4 = $section1->addTable("table4");

$table4->addRow();
$table4->addCell()->addText();$table4->addCell(3800)->addText();$table4->addCell(3800)->addText();
$table4->addCell(3000)->addText("Total: $".number_format(($total-$sale->discount),2,".",","),array("size"=>10, "bold"  => true));

$section1->addText(("Detalle  pago"),array("size"=>12,"color"=>"404040", "bold"  => true));;
$table3 = $section1->addTable("table3");
$table3->addRow();
$table3->addCell(2000)->addText(("Forma de pago"),array("size"=>12,"color"=>"FFFFFF", "bold" => true));;
$table3->addCell(1500)->addText(("Total"),array("size"=>12,"color"=>"FFFFFF", "bold" => true));;

foreach($payments as $pay){
    $table3->addRow();
    $table3->addCell()->addText($pay->name);
    $table3->addCell()->addText("$".number_format($pay->total,2,".",","));
}

$word->addTableStyle('table1', $styleTable,$styleFirstRow);
$word->addTableStyle('table3', $styleTable1,$styleFirstRow);
$word->addTableStyle('table2', $styleTable1,$styleFirstRow);
$word->addTableStyle('table4', $styleTable);

$filename = $client->name.$_GET['id'].'.docx';
#$word->setReadDataOnly(true);
$word->save($filename,"Word2007");
//chmod($filename,0444);
header("Content-Disposition: attachment; filename=$filename");
readfile($filename); // or echo file_get_contents($filename);
unlink($filename);  // remove temp file
