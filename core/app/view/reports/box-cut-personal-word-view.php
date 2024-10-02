<?php
use PhpOffice\PhpWord\Autoloader;
use PhpOffice\PhpWord\Settings;

$word = new  PhpOffice\PhpWord\PhpWord();
$inputs = OperationData::getAllSalesByDates($_GET["startDate"],$_GET["endDate"], $_GET["paymentTypeId"], "all", "all");
$expenses = OperationData::getAllExpensesByDates($_GET["startDate"],$_GET["endDate"]);
$totalInputs = 0;
$totalMedicines = 0;
$totalCash = 0;
$totalCreditCard = 0;
$totalDebitCard = 0;
$totalBankDraft = 0;
$totalExpenses = 0;

$section1 = $word->AddSection();
/*$section1->addImage(
  '../assets/clinic-logo.png',
  array(
    'width' => 160,
    'height' => 75,
    'wrappingStyle' => 'behind'
  )
);*/

$styleTable = array('borderSize' => 2, 'borderColor' => '888888', 'cellMargin' => 40, "size" => 9);
$styleFirstRow = array('borderBottomColor' => '0000FF', "size" => 9);

$table1 = $section1->addTable("table1");
$section1->addText(("CORTE: " . $_GET["id"] . ""), array("size" => 10, "color" => "000000", "bold" => true));


$section1->addText(("INGRESOS"), array("size" => 10, "color" => "404040", "bold"  => true));;
$table2 = $section1->addTable("table3");
$table2->addRow();
$table2->addCell(3000)->addText(("MÉDICO"), array("size" => 10, "color" => "000000", "bold"  => true));
$table2->addCell(3000)->addText(("PACIENTE"), array("size" => 10, "color" => "000000", "bold"  => true));
$table2->addCell(1500)->addText(("CONCEPTOS"), array("size" => 10, "color" => "000000", "bold"  => true));
$table2->addCell(1500)->addText(("F.PAGO"), array("size" => 10, "color" => "000000", "bold"  => true));
$table2->addCell(1500)->addText(("TOTAL"), array("size" => 10, "color" => "000000", "bold"  => true));


foreach ($inputs as $input) {
  $table2->addRow();
  $totalInputs += $input->total;
  $medicsString = OperationData::getMedicsBySaleString($input->id);
  $medicName = (isset($medicsString)) ? $medicsString : "Venta mostrador";

  $table2->addCell()->addText($medicName);
  $table2->addCell()->addText($input->patient_name);

  $operationDetails = OperationDetailData::getAllByOperationId($input->id);

  $productsString = "";

  foreach ($operationDetails as $operationDetail) {
    $product = ProductData::getById($operationDetail->product_id);
    $productsString .= $product->name.",";

    if ($product->type_id == "4") {
      $totalMedicines += $product->price_out * $operationDetail->quantity;
    }
  }
  $productsString = substr($productsString,0,-1);

  $table2->addCell()->addText($productsString);

  $paymentsString = "";

  $payments = OperationPaymentData::getAllByOperationId($input->id);
  foreach ($payments as $payment) {
    $paymentsString .= $payment->getType()->name . ": $" . number_format($payment->total, 2).",";
    if ($payment->payment_type_id == "1") {
      $totalCash += $payment->total;
    } else if ($payment->payment_type_id == "2") {
      $totalDebitCard += $payment->total;
    } else if ($payment->payment_type_id == "3") {
      $totalCreditCard += $payment->total;
    } else if ($payment->payment_type_id == "6") {
      $totalBankDraft += $payment->total;
    }
  }
  $paymentsString = substr($paymentsString,0,-1);
  $table2->addCell()->addText($paymentsString);

  $table2->addCell()->addText("$" . number_format($input->total, 2, ".", ","));
}
$section1->addText(("TOTAL GENERAL: " . number_format($totalInputs, 2)), array("size" => 10, "color" => "000000", "bold"  => true));

$section1->addText(("GASTOS"), array("size" => 10, "color" => "404040", "bold"  => true));;
$table3 = $section1->addTable("table3");
$table3->addRow();
$table3->addCell(2000)->addText(("CONCEPTOS"), array("size" => 10, "color" => "000000", "bold" => true));;
$table3->addCell(1500)->addText(("PRECIO"), array("size" => 10, "color" => "000000", "bold" => true));;
$table3->addCell(1500)->addText(("CANTIDAD"), array("size" => 10, "color" => "000000", "bold" => true));;
$table3->addCell(1500)->addText(("TOTAL"), array("size" => 10, "color" => "000000", "bold" => true));;

foreach ($expenses as $expense) {
  $totalExpenses += $expense->price * $expense->quantity;
  $product = ProductData::getById($expense->product_id);
  $table3->addRow();
  $table3->addCell()->addText($product->name);
  $table3->addCell()->addText($expense->price);
  $table3->addCell()->addText($expense->quantity);
  $table3->addCell()->addText(number_format($expense->price * $expense->quantity, 2));
}
$section1->addText(("TOTAL: " . number_format($totalExpenses, 2)), array("size" => 10, "color" => "000000", "bold"  => true));

$section1->addText(("EFECTIVO: " . number_format($totalCash, 2) . "    " . "SALIDAS: " . number_format($totalExpenses, 2) . "    " . "ENTREGAS: " . number_format($totalCash - $totalExpenses, 2) . "    " . "MEDICAMENTO: " . number_format($totalMedicines, 2) . "    " . "TARJETA: " . number_format($totalCreditCard + $totalDebitCard, 2)), array("size" => 10, "color" => "404040", "bold"  => true));

$word->addTableStyle('table1', $styleTable, $styleFirstRow);
$word->addTableStyle('table3', $styleTable, $styleFirstRow);
$word->addTableStyle('table2', $styleTable, $styleFirstRow);
$word->addTableStyle('table4', $styleTable);

$filename = "CORTE ".$_GET["id"] .".docx";
#$word->setReadDataOnly(true);
$word->save($filename, "Word2007");
//chmod($filename,0444);
header("Content-Disposition: attachment; filename=$filename");
readfile($filename); // or echo file_get_contents($filename);
unlink($filename);  // remove temp file