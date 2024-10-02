<?php
class PatientLaboratoryStudyData {
	public static $tablename = "patient_laboratory_studies";
	public static $tablenameDetails = "patient_laboratory_study_details";
	public $id;
	public $patient_id;
	public $reservation_id;
	public $laboratory_study_id;
	public $date;
	public $created_at;

	public $patient_laboratory_study_id;
	public $laboratory_study_section_id;
	public $laboratory_study_section_option_id;
	public $value;

	public function __construct(){
		$this->created_at = "NOW()";
	}

	public function getPatient()
	{
		return PatientData::getById($this->patient_id);
	}

	public function add(){
		$sql = "INSERT INTO ".self::$tablename." (patient_id,reservation_id,laboratory_study_id,date) ";
		$sql .= "value ($this->patient_id,$this->reservation_id,\"$this->laboratory_study_id\",\"$this->date\")";
		return Executor::doit($sql);
	}

	public function update(){
		$sql = "UPDATE ".self::$tablename." SET date=\"$this->date\" WHERE id = $this->id";
		return Executor::doit($sql);
	}

	public static function getById($id){
		$sql = "SELECT pls.*,DATE_FORMAT(date,'%d/%m/%Y') AS date_format,DATE_FORMAT(date,'%H:%i') AS hour_format 
		FROM ".self::$tablename." pls 
		WHERE id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new PatientLaboratoryStudyData());
	}

	public static function validateByStudyTypeReservation($reservationId,$laboratoryStudyId){
		//Validar si ya existe un estudio de laboratorio en específico asociado a una cita
		$sql = "SELECT * FROM ".self::$tablename." WHERE reservation_id='$reservationId'
		AND laboratory_study_id='$laboratoryStudyId' LIMIT 1";
		$query = Executor::doit($sql);
		return Model::one($query[0],new PatientLaboratoryStudyData());
	}

	public function addDetail(){
		//Guarda el valor de una sección o de una opción referente al estudio de laboratorio de un paciente
		$sql = "INSERT INTO ".self::$tablenameDetails." (patient_laboratory_study_id,laboratory_study_section_id,laboratory_study_section_option_id,value) ";
		$sql .= "value ($this->patient_laboratory_study_id,$this->laboratory_study_section_id,\"$this->laboratory_study_section_option_id\",\"$this->value\")";
		return Executor::doit($sql);
	}

	public function updateDetail(){
		$sql = "UPDATE ".self::$tablenameDetails." SET laboratory_study_section_id=\"$this->laboratory_study_section_id\",laboratory_study_section_option_id=\"$this->laboratory_study_section_option_id\",
		value=\"$this->value\" 
		WHERE id = $this->id";
		return Executor::doit($sql);
	}

	public static function validateDetailByPatientStudyId($patientStudyId,$sectionId,$optionId){
		//Validar si ya existe un estudio de laboratorio en específico asociado a una cita
		$sql = "SELECT * FROM ".self::$tablenameDetails." WHERE patient_laboratory_study_id='$patientStudyId'
		AND laboratory_study_section_id='$sectionId'
		AND laboratory_study_section_option_id='$optionId' 
		LIMIT 1";
		$query = Executor::doit($sql);
		return Model::one($query[0],new PatientLaboratoryStudyData());
	}

	public static function getAllSectionsByStudyId($patientStudyId){
		//Obtiene todas las secciones de un estudio de laboratorio y los valores del paciente,detallando si tiene subopciones
	    $sql = "SELECT ls.id,ls.name, psd.value,
				(SELECT COUNT(so_sq.id) FROM ".LaboratoryStudyData::$tablenameSectionOptions." so_sq WHERE
				so_sq.laboratory_study_section_id = ls.id) total_options
				FROM ".LaboratoryStudyData::$tablenameSections." ls
				LEFT JOIN ".self::$tablenameDetails." psd ON psd.laboratory_study_section_id = ls.id 
				AND psd.patient_laboratory_study_id = '$patientStudyId'
				WHERE ls.laboratory_study_id = (SELECT laboratory_study_id FROM ".self::$tablename." pls WHERE pls.id = '$patientStudyId')
				ORDER BY ls.ordering ASC";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ExplorationExamData());
	}

	public static function getAllOptionsByStudyIdSection($patientStudyId,$sectionId){
	    //Obtiene todas las opciones de una sección de un estudio de laboratorio y los valores del paciente
	    $sql = "SELECT lso.id,lso.name, psd.value 
				FROM ".LaboratoryStudyData::$tablenameSectionOptions." lso
				LEFT JOIN ".self::$tablenameDetails." psd ON psd.laboratory_study_section_option_id = lso.id 
				AND psd.patient_laboratory_study_id = '$patientStudyId'
				WHERE lso.laboratory_study_section_id = '$sectionId'
				ORDER BY lso.ordering ASC";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ExplorationExamData());
	}

	//Obtener toda la información de un estudio de laboratorio con los datos del paciente
	public static function getArraySectionOptionsByPatientStudyId($patientStudyId){
		$arrayData = [];
		$sections = self::getAllSectionsByStudyId($patientStudyId);

		foreach($sections as $section){
			$arrayData[$section->id]["value"] = $section->value;
			$arrayData[$section->id]["options"] = [];
			if($section->total_options > 0){

				$options = self::getAllOptionsByStudyIdSection($patientStudyId,$section->id);
				foreach($options as $option){
					$arrayData[$section->id]["options"][$option->id] = $option->value;
				}
			}
		}
		return $arrayData;
	}
}

?>