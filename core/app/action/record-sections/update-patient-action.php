<?php
if(count($_POST)>0){
    $existsRecord = RecordSectionData::validateByPatient($_POST["patientId"],$_POST["recordSectionId"]);

	if(isset($existsRecord)){
            $existsRecord->value = trim($_POST["value"]);

            if($existsRecord->updateByPatient()) return $existsRecord->value;
            else return http_response_code(500);
	}
	else{
		$record = new RecordSectionData();
		$record->record_section_id = $_POST["recordSectionId"];
		$record->patient_id = $_POST["patientId"];
		$record->value = trim($_POST["value"]);
		if($record->addByPatient()) return $_POST["value"];
		else return http_response_code(500);
	}

}
else return http_response_code(500);
?>