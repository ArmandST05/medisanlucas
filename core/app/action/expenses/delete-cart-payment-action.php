<?php
if(isset($_GET["typePaymentId"])){

	$pay=$_SESSION["expensePaymentTypes"];

		$npay= null;
		$nx=0;
		foreach($pay as $c){
			if($c["id"]!=$_GET["typePaymentId"]){
				$npay[$nx]= $c;
			}
			$nx++;
		}
		$_SESSION["expensePaymentTypes"] = $npay;
	}

print "<script>window.location='index.php?view=expenses/new';</script>";

?>