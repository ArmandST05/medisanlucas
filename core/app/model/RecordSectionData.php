<?php
class RecordSectionData {
	public static $tablename = "record_sections";
	public static $tablenameDetails = "patient_records";

	public static $months = ["01" => "Enero", "02" => "Febrero", "03" => "Marzo", "04" => "Abril", "05" => "Mayo", "06" => "Junio", "07" => "Julio", "08" => "Agosto", "09" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre"];
	
	public function __construct(){
		$this->name = "";
	}

	public function addByPatient(){
		$sql = "INSERT INTO ".self::$tablenameDetails." (patient_id,record_section_id,value) ";
		$sql .= "value (\"$this->patient_id\",\"$this->record_section_id\",\"$this->value\")";
		return Executor::doit($sql);
	}
	
	public function updateByPatient(){
		$sql = "UPDATE ".self::$tablenameDetails." set value=\"$this->value\" WHERE id = $this->id";
		return Executor::doit($sql);
	}

	public static function getAllRecordSections(){
	    $sql = "SELECT * FROM ".self::$tablename." ORDER BY record_sections.name ASC";
		$query = Executor::doit($sql);
		return Model::many($query[0],new RecordSectionData());
	}

	public static function validateByPatient($patientId,$recordSectionId){
	    $sql = "SELECT * FROM ".self::$tablenameDetails." 
		WHERE patient_id = '$patientId' AND record_section_id = '$recordSectionId' 
		LIMIT 1";
		$query = Executor::doit($sql);
		return Model::one($query[0],new RecordSectionData());
	}

	public static function getRecordsByPatient($patientId){
	    $sql = "SELECT ".self::$tablename.".id,".self::$tablename.".name, ".self::$tablenameDetails.".value
		FROM ".self::$tablename." 
		LEFT JOIN ".self::$tablenameDetails." ON ".self::$tablename.".id = ".self::$tablenameDetails.".record_section_id
		AND ".self::$tablenameDetails.".patient_id = '$patientId'
		ORDER BY ".self::$tablename.".name ASC";

		$query = Executor::doit($sql);
		return Model::many($query[0],new RecordSectionData());
	}

	public static function getByRecordIdPatient($patientId,$recordSectionId){
	    $sql = "SELECT ".self::$tablename.".id,".self::$tablename.".name, ".self::$tablenameDetails.".value
		FROM ".self::$tablename." 
		LEFT JOIN ".self::$tablenameDetails." ON ".self::$tablename.".id = ".self::$tablenameDetails.".record_section_id
		AND ".self::$tablenameDetails.".patient_id = '$patientId'
		WHERE ".self::$tablename.".id = '$recordSectionId'";

		$query = Executor::doit($sql);
		return Model::one($query[0],new RecordSectionData());
	}
}
?>