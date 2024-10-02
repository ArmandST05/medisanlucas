<?php
    $search = $_GET["q"];
    $diagnostics = DiagnosticData::getBySearch($search);  
    echo json_encode($diagnostics);
?>