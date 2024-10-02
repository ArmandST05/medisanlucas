<?php
$process="true";
if(isset($_SESSION["expense"])){
	
	$expenseDetails = $_SESSION["expense"];

		if($process==true){
			$operation = new OperationData();
			$operation->user_id = $_SESSION["user_id"];
			$operation->total = $_POST["total"];

			$tot=$_POST["total"];
 			$totalGen=$_POST["totalGen"];
        
            $liq = $tot - $totalGen;

            if($liq==0){
            $operation->status_id = 1;
            }else{
            $operation->status_id = 0;
            }
            $operation->description = $_POST["description"];
 			$addedOperation = $operation->addExpense();
 
		foreach($expenseDetails as  $concept){
			$od = new OperationDetailData();
			$od->product_id = $concept["id"] ;
			$od->operation_type_id = 1;
			$od->operation_id = $addedOperation[1];
			$od->quantity= $concept["quantity"] ;
			$od->price= $concept["cost"];
			$od->date = date("Y-m-d H:i:s");   
			$od->expiration_date= $concept["expirationDate"];
            $add = $od->addExpense();				
		}
		unset($_SESSION["expense"]);
		setcookie("selled","selled");

		if(isset($_SESSION["expensePaymentTypes"])){
		 $payments = $_SESSION["expensePaymentTypes"];

		 foreach($payments as  $paymentDetail){

			$payment = new OperationPaymentData();
			$payment->payment_type_id = $paymentDetail["id"];
			$payment->operation_id = $addedOperation[1];
			$payment->operation_type_id = 1;
			$payment->total = $paymentDetail["total"];
			$payment->date = date("Y-m-d H:i:s");   
			$add = $payment->add();			 		
		}
		
			unset($_SESSION["expensePaymentTypes"]);
			setcookie("selled","selled");
	}

	print "<script>window.location='index.php?view=expenses/index';</script>";
		}
	}
