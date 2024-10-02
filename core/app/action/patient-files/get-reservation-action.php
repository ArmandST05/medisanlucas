<?php
$reservationId = $_GET["reservationId"];
$patientId = $_GET["patientId"];

$files = PatientData::getAllFilesByPatientReservation($patientId, $reservationId);

foreach ($files as $file){
    if(exif_imagetype("storage_data/files/".$file->path)){//Validar si es una imagen
        $imgPath = "storage_data/files/". $file->path;
    }else{
        $imgPath = "assets/default_file.png";
    }
    echo '<div class="col-md-2">
    <a href="storage_data/files/' . $file->path.'" target="__blank"><img width="170px" height="170px" alt="Vista previa no disponible" src="' . $imgPath.'"><br>
         <div class="btn btn-sm btn-default" style="white-space:pre-line">'.$file->path.'</div></img></a>
        <button class="btn btn-sm btn-danger" onclick="deleteFile('.$file->id.')"><i class="fas fa-trash"></i></button>
    </div>';
}
