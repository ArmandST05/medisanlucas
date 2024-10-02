<?php
$process=true;
if(isset($_SESSION["cartOutputs"])){
	$cart = $_SESSION["cartOutputs"];
	
		if($process==true){
			$operation = new OperationData();
			$operation->user_id = $_SESSION["user_id"];
			$operation->total = 0;
			$operation->description = $_POST["description"];
 		    $output = $operation->addOutput();
		
		foreach($cart as $cartDetail){
			 $detail = new OperationDetailData();
			 $detail->product_id = $cartDetail["id"] ;
			 $detail->operation_type_id = 2;
			 $detail->operation_id = $output[1];
			 $detail->quantity = $cartDetail["quantity"];
			 $detail->price= 0;
			 $detail->date = date("Y-m-d");

			if(isset($_POST["is_oficial"])){
				$detail->is_oficial = 1;
			}
			$add = $detail->add();			 		

			unset($_SESSION["cartOutputs"]);
			setcookie("selled","selled");
		}
		}
		print "<script>window.location='index.php?view=inventory/index-outputs';</script>";
	}


?>
