<?php
//upload_max_filesize = 512M (MAXIMUM)-HOSTGATOR PHP.INI CONFIGURATION

    $file = PatientData::getFileById($_POST["id"]);
    $targetFilePath = "storage_data/files/" . $file->path;

    // Check if file already exists
    if (unlink($targetFilePath)) {
        $file->deleteFile();
        return http_response_code(200);
    } else {
        return http_response_code(500);
    }
