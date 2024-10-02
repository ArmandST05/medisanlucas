<?php
if(isset($_GET["paymentTypeId"])){

	$payments = $_SESSION["payments"];

		$npay= null;
		$nx=0;
		foreach($payments as $payment){
			if($payment["id"] != $_GET["paymentTypeId"]){
				$npay[$nx]= $payment;
			}
			$nx++;
		}
		$_SESSION["payments"] = $npay;
	}

print "<script>window.location='index.php?view=sales/new-details&patientId=".$_GET['patientId']."&date=".$_GET['date']."';</script>";
?>