<?php
$operationDetails = OperationPaymentData::getAllByOperationId($_POST["operationId"]);
$num_succ = 0;
$process=false;
$errors = array();

	$quantity = OperationDetailData::getStockByProduct($_POST["productId"]);
	
	if($_POST["quantity"]<=$quantity){
		$num_succ++;
		$process = true;

	}else{
		$error = array("id"=>$_POST["productId"],"message"=>"No hay suficiente cantidad de producto en inventario.");
		$errors[count($errors)] = $error;
	}

	if($process==false){	
		echo '<script>
			alert("No tiene existencias suficientes");
			window.location="index.php?view=inventory/edit-output&id='.$_POST["operationId"].'";
		</script>';
	}

	$found = false;
	$index=0;
	$quantity = OperationDetailData::getStockByProduct($_POST["productId"]);


	$can = true;
	if($_POST["quantity"]<=$quantity){
	}else{
		$can=false;
	}

	if($can==false){
		echo '<script>
			alert("No tiene existencias suficientes");
			window.location="index.php?view=inventory/edit-output&id='.$_POST["operationId"].'";
		</script>';
	}

	if($can==true){
		foreach($operationDetails as $c){
			if($c->product_id==$_POST["productId"]){
				$found=true;
				break;
			}
			$index++;

		}

		if($found==true){
			echo '<script> 
					alert("El producto ya esta en la lista");
					window.location="index.php?view=inventory/edit-output&id='.$_POST["operationId"].'";
				</script>
			';				
		}

		if($found==false){
			$operationDetail = new OperationDetailData();
			$operationDetail->product_id = $_POST["productId"];
			$operationDetail->operation_type_id = 2;
			$operationDetail->operation_id = $_POST["operationId"];
			$operationDetail->quantity = $_POST["quantity"];
			$operationDetail->price = 0;
			$operationDetail->date = date("Y-m-d");
			$add = $operationDetail->add();
		}
}
print "<script>window.location='index.php?view=inventory/edit-output&id=".$_POST["operationId"]."';</script>";

?>