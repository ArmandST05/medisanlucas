<?php
if(isset($_GET["idTypePay"])){

	$pay=$_SESSION["typePM"];

		$npay= null;
		$nx=0;
		foreach($pay as $c){
			if($c["idType"]!=$_GET["idTypePay"]){
				$npay[$nx]= $c;
			}
			$nx++;
		}
		$_SESSION["typePM"] = $npay;
	}


print "<script>window.location='index.php?view=re';</script>";

?>