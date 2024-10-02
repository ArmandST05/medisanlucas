<?php
class SuiveFormatData {
	public static $tablename = "suive_formats";
	public static $tablenameInstitutions = "suive_institutions";
	public static $tablenameAgeRanges = "suive_age_ranges";
	public static $tablenameDetails = "suive_format_details";
	public static $tablenameFiles = "suive_format_files";

	public function __construct(){
		$this->week_number = "";
		$this->start_date = "";
		$this->end_date = "";
		$this->user_id = "";
	}

	public function getUser(){ 
		return UserData::getById($this->user_id); 
	}

	/*public function add(){
		$sql = "INSERT INTO ".self::$tablename." (week_number,start_date,end_date,user_id) ";
		$sql .= "value ($this->week_number,\"$this->start_date\",\"$this->end_date\",\"$this->user_id\")";
		return Executor::doit($sql);
	}*/

	public function add(){
		$sql = "INSERT INTO ".self::$tablename." (week_number,start_date,end_date,user_id,unity,suave_unity_code,clues,community_name,county_id,jurisdiction_name,state_id,institution_id,institution_name,path) ";
		$sql .= "value ($this->week_number,\"$this->start_date\",\"$this->end_date\",\"$this->user_id\",\"$this->unity\",\"$this->suave_unity_code\",\"$this->clues\",\"$this->community_name\",\"$this->county_id\",\"$this->jurisdiction_name\",\"$this->state_id\",\"$this->institution_id\",\"$this->institution_name\",\"$this->path\")";
		return Executor::doit($sql);
	}

	/*public function update(){
		$sql = "UPDATE ".self::$tablename." set week_number=\"$this->week_number\",start_date=\"$this->start_date\",end_date=\"$this->end_date\",user_id=\"$this->user_id\" WHERE id = $this->id";
		Executor::doit($sql);
	}*/

	public function update(){
		$sql = "UPDATE ".self::$tablename." set week_number=\"$this->week_number\",start_date=\"$this->start_date\",end_date=\"$this->end_date\",user_id=\"$this->user_id\",unity=\"$this->unity\",suave_unity_code=\"$this->suave_unity_code\",clues=\"$this->clues\",community_name=\"$this->community_name\",county_id=\"$this->county_id\",jurisdiction_name=\"$this->jurisdiction_name\",state_id=\"$this->state_id\",institution_id=\"$this->institution_id\",institution_name=\"$this->institution_name\" WHERE id = $this->id";
		Executor::doit($sql);
	}

	public function updateFile(){
		$sql = "UPDATE ".self::$tablename." set path=\"$this->path\" WHERE id = $this->id";
		Executor::doit($sql);
	}

	public static function getById($id){
		$sql = "SELECT ".self::$tablename.".*,
		DATE_FORMAT(".self::$tablename.".start_date,'d%/m%/Y%') AS start_date_format,
		DATE_FORMAT(".self::$tablename.".end_date,'d%/m%/Y%') AS end_date_format
		FROM ".self::$tablename." 
		WHERE id = $id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new SuiveFormatData());
	}

	public static function getAll(){
		$sql = "SELECT ".self::$tablename.".*, DATE_FORMAT(".self::$tablename.".start_date,'%Y') AS year
			FROM ".self::$tablename." 
			ORDER BY start_date";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SuiveFormatData());
	}

	public function delete(){
		$sql = "DELETE FROM ".self::$tablename." 
		WHERE id=$this->id";
		Executor::doit($sql);
	}

	/*--------------INSTITUCIONES------- */
	public static function getAllInstitutions(){
		$sql = "SELECT ".self::$tablenameInstitutions.".* FROM ".self::$tablenameInstitutions."";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SuiveFormatData());
	}

	public static function getAllAgeRanges(){
		$sql = "SELECT * FROM ".self::$tablenameAgeRanges." ORDER BY id ASC";
		$query = Executor::doit($sql);
		return Model::many($query[0],new SuiveFormatData());
	}

	public static function getReportById($id){
		$suiveFormat = SuiveFormatData::getById($id);
		$startDate = $suiveFormat->start_date;
		$endDate = $suiveFormat->end_date;
		
		$diagnostics = DiagnosticData::getAllSuiveNotification();
		$ageRanges = SuiveFormatData::getAllAgeRanges();

		$suiveDiagnostics = [];
		$cnt = 0;
		foreach($diagnostics as $diagnostic){
			$totalMale = 0;
			$totalFemale= 0;

			$suiveDiagnostics[$cnt] = new SuiveFormatData();
			$suiveDiagnostics[$cnt]->id = $diagnostic->id;
			$suiveDiagnostics[$cnt]->name = $diagnostic->name;
			$suiveDiagnostics[$cnt]->epi_code = $diagnostic->EPI_CLAVE;
			$detailsArray = [];
			foreach($ageRanges as $ageRange){
				$male = DiagnosticData::getTotalByDiagnosticIdSexRange($diagnostic->id,1,$startDate,$endDate,$ageRange->start,$ageRange->end)->total;
				$totalMale += $male;
				$detailsArray[] = $male;//Masculino
				$female = DiagnosticData::getTotalByDiagnosticIdSexRange($diagnostic->id,2,$startDate,$endDate,$ageRange->start,$ageRange->end)->total;
				$detailsArray[] = $female;//Femenino
				$totalFemale += $female;
			}

			$male = DiagnosticData::getTotalByDiagnosticIdSexNonAge($diagnostic->id,1,$startDate,$endDate,$ageRange->start,$ageRange->end)->total;
			$totalMale += $male;
			$detailsArray[] = $male;//Masculino
			$female = DiagnosticData::getTotalByDiagnosticIdSexNonAge($diagnostic->id,2,$startDate,$endDate,$ageRange->start,$ageRange->end)->total;
			$detailsArray[] = $female;//Femenino
			$totalFemale += $female;

			$suiveDiagnostics[$cnt]->total_male = $totalMale;
			$suiveDiagnostics[$cnt]->total_female = $totalFemale;
			$suiveDiagnostics[$cnt]->total = ($totalMale +$totalFemale);
			$suiveDiagnostics[$cnt]->details = $detailsArray;
			$cnt++;
		}
		return $suiveDiagnostics;
	}
	

}
