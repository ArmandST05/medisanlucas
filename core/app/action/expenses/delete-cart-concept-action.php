<?php
if(isset($_GET["conceptId"])){

	$buy=$_SESSION["expense"];
		$nbuy= null;
		$nx=0;
		foreach($buy as $c){
			if($c["id"] != $_GET["conceptId"]){
				$nbuy[$nx]= $c;
			}
			$nx++;
		}
		$_SESSION["expense"] = $nbuy;
	}

print "<script>window.location='index.php?view=expenses/new';</script>";

?>