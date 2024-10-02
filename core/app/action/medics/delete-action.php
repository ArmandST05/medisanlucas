<?php

$medic = MedicData::getById($_GET["id"]);
if($medic->delete()){
    //Remove all uploaded files
    $directoryPath = "storage_data/medics/" . $_POST['id'] . "/";
    ConfigurationData::deleteDirectory($directoryPath);
}

Core::redir("./index.php?view=medics/index");

?>