<?php
if(count($_POST)>0){
	$newPatient = new PatientData();
  //Validar si el nombre del paciente está registrado
  $isRegistered = PatientData::getByName(trim($_POST["name"]));

  if(!$isRegistered){
    $newPatient->name = trim($_POST["name"]);
    $newPatient->sex_id = (!isset($_POST["sex"]) ? 1: $_POST["sex"]);
    $newPatient->curp = $_POST["curp"];
    $newPatient->street = trim($_POST["street"]);
    $newPatient->number = trim($_POST["number"]);
    $newPatient->colony = trim($_POST["colony"]);
    $newPatient->cellphone = trim($_POST["cellphone"]);
    $newPatient->homephone = trim($_POST["homephone"]);
    $newPatient->email = trim($_POST["email"]);
    $newPatient->birthday = ($_POST["birthday"]) ? $_POST["birthday"]: null;
    $newPatient->referred_by = trim($_POST["referred_by"]);
    $newPatient->relative_name = trim($_POST["relative_name"]);
    $newPatient->category_id = (!isset($_POST["category_id"]) ? 1: $_POST["category_id"]);
    $newPatient->image = "";

   if(strlen($_POST['image'])>6){
      $image_data = $_POST["image"];

      $image_array_1 = explode(";", $image_data);
      $image_array_2 = explode(",", $image_array_1[1]);

      $image_data = base64_decode($image_array_2[1]);

      $imageName = time().'.jpg';

      if(!is_dir("storage_data/patients/")){
        mkdir("storage_data/patients/", 0755);
      }

      if(file_put_contents("storage_data/patients/".$imageName, $image_data)){
        $newPatient->image = $imageName;
      }
   }

   $addPatient = $newPatient->add();
    if($addPatient){
      echo $addPatient[0];
    }else{
      return http_response_code(500);
    }
  }
  else{
    return http_response_code(500);
    Core::alert("Paciente: ".$_POST["name"]." ya está registrado");
    //print "<script>window.location='index.php?view=patients/index';</script>";
  }

}
?>
