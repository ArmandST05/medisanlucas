<?php
    $search = $_GET["q"];
    $medicines = MedicineData::getBySearch($search);  
    echo json_encode($medicines);
?>