<?php
//upload_max_filesize = 512M (MAXIMUM)-HOSTGATOR PHP.INI CONFIGURATION

function compressImage($source, $destination, $quality)
{
    // Obtenemos la información de la imagen
    $fileInfo = getimagesize($source);
    $mime = $fileInfo['mime'];

    // Creamos una imagen
    switch ($mime) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($source);
            break;
        case 'image/png':
            $image = imagecreatefrompng($source);
            break;
        case 'image/gif':
            $image = imagecreatefromgif($source);
            break;
        default:
            $image = imagecreatefromjpeg($source);
    }

    // Guardamos la imagen
    imagejpeg($image, $destination, $quality);

    // Devolvemos la imagen comprimida
    return $destination;
}

function formatFileName($string){

    $string = str_replace(".","",$string);
    $string = str_replace(" ","",$string);

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
    $string );

    //Reemplazamos la I y i
    $string = str_replace(
    array('Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î'),
    array('I', 'I', 'I', 'I', 'i', 'i', 'i', 'i'),
    $string );

    //Reemplazamos la O y o
    $string = str_replace(
    array('Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô'),
    array('O', 'O', 'O', 'O', 'o', 'o', 'o', 'o'),
    $string );

    //Reemplazamos la U y u
    $string = str_replace(
    array('Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û'),
    array('U', 'U', 'U', 'U', 'u', 'u', 'u', 'u'),
    $string );

    //Reemplazamos la N, n, C y c
    $string = str_replace(
    array('Ñ', 'ñ', 'Ç', 'ç'),
    array('N', 'n', 'C', 'c'),
    $string
    );
    
    return $string;
}


if (!empty($_FILES["files"]["name"])) {
    $originalFileName = $_FILES["files"]["name"];
    $fileName = $_FILES["files"]["name"];
    
    $fileName = formatFileName($fileName);

    // Image temp source 
    $fileTemp = $_FILES["files"]["tmp_name"];

    // Comprimos el fichero
    /*if (compressImage($fileTemp, $targetFilePath, 75)) {

        $file = new PatientData();
        $file->patient_id = $_POST["patientId"];
        $file->reservation_id = $_POST["reservationId"];
        $file->path = $fileName;
        if ($file->addFile()) echo $targetFilePath;
        else return http_response_code(500);
    } else {
        return http_response_code(500);
    }*/
    // Where the file is going to be stored
    /*
    $target_dir = "upload/";
    $file = $_FILES['my_file']['name'];
    $path = pathinfo($file);
    $filename = $path['filename'];
    $ext = $path['extension'];
    $temp_name = $_FILES['my_file']['tmp_name'];*/

    $path = pathinfo($originalFileName);
    $ext = $path['extension'];

    //Crear carpeta de archivos si no existe
	if (!file_exists("storage_data/files/")) {
		mkdir("storage_data/files/", 0777, true);
	}

    $targetFilePath = "storage_data/files/" . $fileName. "." . $ext;

    $pathFilenameExtension = $targetFilePath;

    // Check if file already exists
    if (file_exists($pathFilenameExtension)) {
        return http_response_code(500);
    } else {
        if (move_uploaded_file($fileTemp, $targetFilePath)) {
            $file = new PatientData();
            $file->patient_id = $_POST["patientId"];
            $file->reservation_id = $_POST["reservationId"];
            $file->path = $fileName. "." . $ext;
            if ($file->addFile()) echo $targetFilePath;
            else return http_response_code(500);
        }
    }
}
?>