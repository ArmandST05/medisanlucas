<?php
if(count($_POST)>0){
	$user = new UserData();
	$user->name = $_POST["name"];
	$user->username = $_POST["username"];
	$user->password = sha1(md5($_POST["password"]));
	$user->user_type = $_POST["user_type"];
	$user->add();

print "<script>window.location='index.php?view=users/index';</script>";
}
?>