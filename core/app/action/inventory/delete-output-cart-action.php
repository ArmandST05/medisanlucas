<?php
if(isset($_GET["productId"])){
	$cart=$_SESSION["cartOutputs"];
	if(count($cart)==1){
	 unset($_SESSION["cartOutputs"]);
	}else{
		$ncart = null;
		$nx=0;
		foreach($cart as $c){
			if($c["id"]!=$_GET["productId"]){
				$ncart[$nx]= $c;
			}
			$nx++;
		}
		$_SESSION["cartOutputs"] = $ncart;
	}

}else{
 unset($_SESSION["cartOutputs"]);
}

print "<script>history.back();</script>";

?>