<?php
if(isset($_SESSION["reabastecer"])){
	$cart = $_SESSION["reabastecer"];
	if(count($cart)>0){

$process = true;

//////////////////////////////////
		if($process==true){
			$sell = new OperationData();
			$sell->user_id = $_SESSION["user_id"];
			$sell->total = $_POST["total"];
			 
 	    	$s = $sell->add_re();
			

		foreach($cart as  $c){


			$op = new OperationDetailData();
			 $op->product_id = $c["product_id"] ;
			 $op->operation_type_id=1; // 1 - entrada
			 $op->sell_id=$s[1];
			 $op->q= $c["q"];
             $op->price= $c["precio"];

			if(isset($_POST["is_oficial"])){
				$op->is_oficial = 1;
			}

			$add = $op->add();			 		

		}
			unset($_SESSION["reabastecer"]);
			unset($_SESSION["typePM"]);
			setcookie("selled","selled");
////////////////////
print "<script>window.location='index.php?view=onere&id=$s[1]';</script>";
		}
	}
}



?>
