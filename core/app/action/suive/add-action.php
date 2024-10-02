<?php
if(count($_POST)>0){
  $format = new SuiveFormatData();
  $format->week_number = $_POST["weekNumber"];
  $format->start_date = $_POST["startDate"];
  $format->end_date = $_POST["endDate"];
  $format->user_id = $_SESSION["user_id"];
  $format->unity = $_POST["unity"];
  $format->suave_unity_code = $_POST["suaveUnityCode"];
  $format->clues = $_POST["clues"];
  $format->community_name = $_POST["communityName"];
  $format->county_id = $_POST["countyId"];
  $format->jurisdiction_name = $_POST["jurisdictionName"];
  $format->state_id = $_POST["stateId"];
  $format->institution_id = $_POST["institutionId"];
  $format->institution_name = $_POST["institutionName"];
  $format->path = "";

  
	function formatFileName($string)
	{

		$string = str_replace(".", "", $string);
		$string = str_replace(" ", "", $string);

		//Reemplazamos la A y a
		$string = str_replace(
			array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª'),
			array('A', 'A', 'A', 'A', 'a', 'a', 'a', 'a', 'a'),
			$string
		);

		//Reemplazamos la E y e
		$string = str_replace(
			array('É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê'),
			array('E', 'E', 'E', 'E', 'e', 'e', 'e', 'e'),
			$string
		);

		//Reemplazamos la I y i
		$string = str_replace(
			array('Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î'),
			array('I', 'I', 'I', 'I', 'i', 'i', 'i', 'i'),
			$string
		);

		//Reemplazamos la O y o
		$string = str_replace(
			array('Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô'),
			array('O', 'O', 'O', 'O', 'o', 'o', 'o', 'o'),
			$string
		);

		//Reemplazamos la U y u
		$string = str_replace(
			array('Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û'),
			array('U', 'U', 'U', 'U', 'u', 'u', 'u', 'u'),
			$string
		);

		//Reemplazamos la N, n, C y c
		$string = str_replace(
			array('Ñ', 'ñ', 'Ç', 'ç'),
			array('N', 'n', 'C', 'c'),
			$string
		);

		return $string;
	}

	if (isset($_FILES["file"]) && ($_FILES["file"]["size"] > 0)) {
		$originalFileName = $_FILES["file"]["name"];
		$fileName = $_FILES["file"]["name"];
		$fileName = formatFileName($fileName);
		// File temp source 
		$fileTemp = $_FILES["file"]["tmp_name"];
		$path = pathinfo($originalFileName);
		$ext = $path['extension'];
		$targetFilePath = "storage_data/suive/". $fileName . "." . $ext;
		$pathFilenameExtension = $targetFilePath . "." . $ext;

		//Guardar archivo
		if (move_uploaded_file($fileTemp, $targetFilePath)) {
			$format->path = $fileName. "." . $ext;
		}
	}
  $addedFormat = $format->add();

  print "<script>window.location='index.php?view=suive/index';</script>";
}
