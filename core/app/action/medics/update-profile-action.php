<?php
if (count($_POST) > 0) {
	$medic = MedicData::getById($_POST["id"]);
	$medic->professional_license = trim($_POST["professionalLicense"]);
	$medic->study_center = trim($_POST["studyCenter"]);
	$medic->email = trim($_POST["email"]);
	$medic->phone = trim($_POST["phone"]);
	$medic->other_specialties = trim($_POST["otherSpecialties"]);
	$medic->is_digital_signature = (isset($_POST["isDigitalSignature"])) ? $_POST["isDigitalSignature"]:0;
	$medic->is_fiel_key = (isset($_POST["isFielKey"])) ? $_POST["isFielKey"]:0;
	$medic->is_study_center_prescription = (isset($_POST["isStudyCenterPrescription"])) ? $_POST["isStudyCenterPrescription"]:0;
	if ($_POST["fielKeyPassword"] != "") {
		$medic->fiel_key_password = $_POST["fielKeyPassword"];
		$medic->updateFielKeyPassword();
	}
	$medic->updateProfile();

	//Crear carpeta del médico si no existe
	$path = "storage_data/medics/" . $_POST['id'];
	if (!file_exists($path)) {
		mkdir($path, 0777, true);
	}

	if ($medic->is_study_center_prescription == 1 && isset($_FILES["studyCenterLogo"]) && ($_FILES["studyCenterLogo"]["size"] > 0)) {
		$originalFileName = $_FILES["studyCenterLogo"]["name"];
		$fileName = "studyCenterLogo.png";
		// File temp source 
		$fileTemp = $_FILES["studyCenterLogo"]["tmp_name"];
		$path = pathinfo($originalFileName);
		$ext = $path['extension'];
		$targetFilePath = "storage_data/medics/" . $_POST['id'] . "/" . $fileName;
		$pathFilenameExtension = $targetFilePath . "." . $ext;

		//Si hay un archivo del mismo nombre, eliminar
		if (file_exists($pathFilenameExtension)) {
			unlink($pathFilenameExtension);
		}
		//Guardar archivo
		if (move_uploaded_file($fileTemp, $targetFilePath)) {
			$medic->study_center_logo = $fileName;
			$medic->updateStudyCenterLogo();
		}
	}

	if (isset($_FILES["digitalSignature"]) && ($_FILES["digitalSignature"]["size"] > 0)) {
		$originalFileName = $_FILES["digitalSignature"]["name"];
		$fileName = $_FILES["digitalSignature"]["name"];
		$fileName = ConfigurationData::formatFileName($fileName);
		// File temp source 
		$fileTemp = $_FILES["digitalSignature"]["tmp_name"];
		$path = pathinfo($originalFileName);
		$ext = $path['extension'];
		$targetFilePath = "storage_data/medics/" . $_POST['id'] . "/" . $fileName . "." . $ext;
		$pathFilenameExtension = $targetFilePath . "." . $ext;

		//Si hay un archivo del mismo nombre, eliminar
		if (file_exists($pathFilenameExtension)) {
			unlink($pathFilenameExtension);
		}
		//Guardar archivo
		if (move_uploaded_file($fileTemp, $targetFilePath)) {
			$medic->digital_signature_path = $fileName. "." . $ext;
			$medic->updateDigitalSignature();
		}
	}
	if (isset($_FILES["fielKey"]) && ($_FILES["fielKey"]["size"] > 0)) {
		$originalFileName = $_FILES["fielKey"]["name"];
		$fileName = $_FILES["fielKey"]["name"];
		$fileName = ConfigurationData::formatFileName($fileName);
		// File temp source 
		$fileTemp = $_FILES["fielKey"]["tmp_name"];
		$path = pathinfo($originalFileName);
		$ext = $path['extension'];
		$targetFilePath = "storage_data/medics/" . $_POST['id'] . "/" . $fileName . "." . $ext;
		$pathFilenameExtension = $targetFilePath . "." . $ext;

		//Si hay un archivo del mismo nombre, eliminar
		if (file_exists($pathFilenameExtension)) {
			unlink($pathFilenameExtension);
		}
		//Guardar archivo
		if (move_uploaded_file($fileTemp, $targetFilePath)) {
			$medic->fiel_key_path = $fileName. "." . $ext;
			$medic->updateFielKey();
		}
	}
	if (isset($_FILES["fielCertificate"]) && ($_FILES["fielCertificate"]["size"] > 0)) {
		$originalFileName = $_FILES["fielCertificate"]["name"];
		$fileName = $_FILES["fielCertificate"]["name"];
		$fileName = ConfigurationData::formatFileName($fileName);
		// File temp source 
		$fileTemp = $_FILES["fielCertificate"]["tmp_name"];
		$path = pathinfo($originalFileName);
		$ext = $path['extension'];
		$targetFilePath = "storage_data/medics/" . $_POST['id'] . "/" . $fileName . "." . $ext;
		$pathFilenameExtension = $targetFilePath . "." . $ext;

		//Si hay un archivo del mismo nombre, eliminar
		if (file_exists($pathFilenameExtension)) {
			unlink($pathFilenameExtension);
		}
		//Guardar archivo
		if (move_uploaded_file($fileTemp, $targetFilePath)) {
			$medic->fiel_certificate_path = $fileName. "." . $ext;
			$medic->updateFielCertificate();
		}
	}
	print "<script>window.location='index.php?view=configuration/edit-medic-profile';</script>";
}
