<?php
if(count($_POST)>0){
	$patient = PatientData::getById($_POST["user_id"]);
	$patient->name = trim($_POST["name"]);
	$patient->sex_id = (!isset($_POST["sex"]) ? 1: $_POST["sex"]);
	$patient->curp = $_POST["curp"];
	$patient->relative_name = trim($_POST["relative_name"]);
	$patient->street = trim($_POST["street"]);
	$patient->number = $_POST["number"];
	$patient->colony = trim($_POST["colony"]);
	$patient->cellphone = $_POST["cellphone"];
	$patient->homephone = $_POST["homephone"];
	$patient->email = trim($_POST["email"]);
	$patient->birthday = $_POST["birthday"];
	$patient->referred_by = $_POST["referred_by"];
	$patient->category_id = (!isset($_POST["category_id"]) ? 1: $_POST["category_id"]);

	if(!is_dir("storage_data/patients/")){
		mkdir("storage_data/patients/", 0755);
	}
	
	if($patient->image != null){
		$url = "/storage_data/patients/".$patient->image;
		$deleteImage =  getcwd().$url;
		unlink($deleteImage);
	}
	$patient->image = ""; 

	if(strlen($_POST['image'])>6){
	$image_data = $_POST["image"];

	$image_array_1 = explode(";", $image_data);
	$image_array_2 = explode(",", $image_array_1[1]);

	$image_data = base64_decode($image_array_2[1]);
	$imageName = time().'.jpg';

	if(file_put_contents("storage_data/patients/".$imageName, $image_data)){
		$patient->image = $imageName;
	}
	}

	if($patient->update()){
		return http_response_code(200);
	}
	else{
		return http_response_code(500);
	}
}
else{
	return http_response_code(500);
}
?>