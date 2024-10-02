<?php
	// define('LBROOT',getcwd()); // LegoBox Root ... the server root
	// include("core/controller/Database.php");

	if(!isset($_SESSION["user_id"])) {
		$user = $_POST['username'];
		$pass = sha1(md5($_POST['password']));

		$base = new Database();
		$con = $base->connect();
		$sql = "select * from users where (email= \"".$user."\" or username= \"".$user."\") and password= \"".$pass."\"";
	
		$query = $con->query($sql);
		$found = false;
		$userid = null; 
		$username = null;
		$type = null;

		while($r = $query->fetch_array()){
			$found = true ;
			$userid = $r['id'];
			$username = $r['username'];
			$type = $r['user_type'];
		}
		$_SESSION['typeUser'] = $type;

		if($found==true) {
			$_SESSION['user_id'] = $userid ;
			print "Cargando ... $user";
			print "<script>window.location='index.php?view=home';</script>";
		}else {
			print "<script>
				alert('Verifica tus datos'); 
				window.location='index.php?view=login';
			</script>";
		}

	}else{
		print "<script>
			alert('Verifica tus datos');
			window.location='index.php?view=login';
		</script>";
	}
?>