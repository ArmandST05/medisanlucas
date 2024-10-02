<?php
//ACTUALIZAR ESTUDIO DE LABORATORIO COVID-19
if(count($_POST)>0){
	$patientStudyId = $_POST["patientLaboratoryStudyId"];
	$patientStudy = PatientLaboratoryStudyData::getById($patientStudyId);
	$patientStudy->date = $_POST["date"];
	$patientStudy->update();

	$sectionsPost = $_POST["sections"];//Valores de secciones y opciones capturados por el usuario

	//Recorrer secciones y opciones por defecto del estudio de laboratorio
	$sections = PatientLaboratoryStudyData::getAllSectionsByStudyId($patientStudyId);

	foreach($sections as $section){//Recorrer secciones de estudio de laboratorio
		$arrayData[$section->id]["value"] = $section->value;
		$arrayData[$section->id]["options"] = [];

		if($section->total_options > 0){//Hay opciones, recorrer opciones para guardar cada valor

			$options = PatientLaboratoryStudyData::getAllOptionsByStudyIdSection($patientStudyId,$section->id);

			foreach($options as $option){//Recorrer opciones de estudio de laboratorio

				$validateDetail = PatientLaboratoryStudyData::validateDetailByPatientStudyId($patientStudyId,$section->id,$option->id);
				$value = (isset($sectionsPost[$section->id][$option->id])) ? $sectionsPost[$section->id][$option->id]:null;

				if($validateDetail){//Si ya existe actualizar
					$validateDetail->patient_laboratory_study_id = $patientStudyId;
					$validateDetail->laboratory_study_section_id = $section->id;
					$validateDetail->laboratory_study_section_option_id = $option->id;
					$validateDetail->value = $value;
					$updateDetail = $validateDetail->updateDetail();
				}else{//Si no existe, crear
					$detail = new PatientLaboratoryStudyData();
					$detail->patient_laboratory_study_id = $patientStudyId;
					$detail->laboratory_study_section_id = $section->id;
					$detail->laboratory_study_section_option_id = $option->id;
					$detail->value = $value;
					$newDetail = $detail->addDetail();
				}
			}
		}else{
			//No tiene opciones, se guarda el valor
			$validateDetail = PatientLaboratoryStudyData::validateDetailByPatientStudyId($patientStudyId,$section->id,0);
			$value = (isset($sectionsPost[$section->id])) ? $sectionsPost[$section->id]:null;

			if($validateDetail){//Si ya existe actualizar
				$validateDetail->patient_laboratory_study_id = $patientStudyId;
				$validateDetail->laboratory_study_section_id = $section->id;
				$validateDetail->laboratory_study_section_option_id = 0;
				$validateDetail->value = $value;
				$updateDetail = $validateDetail->updateDetail();
			}else{//Si no existe, crear
				$detail = new PatientLaboratoryStudyData();
				$detail->patient_laboratory_study_id = $patientStudyId;
				$detail->laboratory_study_section_id = $section->id;
				$detail->laboratory_study_section_option_id = 0;
				$detail->value = $value;
				$newDetail = $detail->addDetail();
			}
		}
	}

}
Core::redir("./index.php?view=laboratory-studies/study-1&id=".$patientStudyId);

?>