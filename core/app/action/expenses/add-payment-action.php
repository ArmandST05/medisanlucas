<?php
if(count($_POST)>0){
	$payment = new OperationPaymentData();
    $payment->payment_type_id = $_POST["paymentTypeId"] ;
    $payment->operation_id=$_POST["expenseId"];
    $payment->operation_type_id = 1;
    $payment->total= $_POST["total"];
    $payment->date= $_POST["date"];
	$payment->add();

	$operation = OperationData::getById($_POST["expenseId"]);
    $operation->created_at = $_POST["date"];	
    $operation->updateDate();	

    OperationDetailData::updateDate($_POST["expenseId"], $_POST["date"]);
		
    print "<script>window.location='index.php?view=expenses/edit&id=".$_POST["expenseId"]."';</script>";
}
?>