<?php
class PatientData {
	public static $tablename = "patients";
	public static $tablenameFiles = "patient_files";
	public static $tablenameCategories = "patient_categories";
	public static $months = ["01" => "Enero", "02" => "Febrero", "03" => "Marzo", "04" => "Abril", "05" => "Mayo", "06" => "Junio", "07" => "Julio", "08" => "Agosto", "09" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre"];
	
	public $id;
	public $name;
	public $sex_id;
	public $curp;
	public $street;
	public $number;
	public $colony;
	public $cellphone;
	public $homephone;
	public $email;
	public $birthday;
	public $referred_by;
	public $relative_name;
	public $category_id;
	public $image;
	public $medic_id;
	public $county_id;
	public $notes;
	public $reservation_id;
	public $patient_id;
	public $path;
	public $created_at;

	public function __construct(){
		$this->email = "";
		$this->image = "";
		$this->created_at = "NOW()";
	}

	public function getAge(){
		if ($this->birthday && $this->birthday != "0000-00-00") {
		//Edad del paciente
		$date2 = date('Y-m-d');
		$diff = abs(strtotime($date2) - strtotime($this->birthday));
		$years = floor($diff / (365 * 60 * 60 * 24));
		if ($years == 1) {
		return $years = $years . " Año";
		} else {
		return $years = $years . " Años";
		}		
	} else {
			return "No especificada.";
		}
	}

	public function getAgeByDate($date)
	{
		//Calcula la edad del paciente en una fecha determinada
		if ($this->birthday && $this->birthday != "0000-00-00") {
			//Edad del paciente
			$diff = abs(strtotime($date) - strtotime($this->birthday));
			$years = floor($diff / (365 * 60 * 60 * 24));
			if ($years == 1) {
				return $years = $years . " Año";
			} else {
				return $years = $years . " Años";
			}
		} else {
			return "No especificada.";
		}
	}

	public function getBirthdayFormat()
	{
		//Obtiene la fecha de nacimiento con el nombre del mes del paciente
		if ($this->birthday && $this->birthday != "0000-00-00") {
			$day = substr($this->birthday, 8, 2);
			$month = substr($this->birthday, 5, 2);
			$year = substr($this->birthday, 0, 4);

			return $day . "/" . self::$months[$month] . "/" . $year;
		} else {
			return "No especificada.";
		}
	}

	public function getMedic(){ 
		return MedicData::getById($this->medic_id); 
	}

	public function getCounty(){ 
		if($this->county_id){
			return CountyData::getById($this->county_id); 
		}else{
			return new CountyData(); 
		}
	}
	public function getSex(){ 
		return SexData::getById($this->sex_id); 
	}

	public function add(){
		$sql = "INSERT INTO ".self::$tablename." (name,sex_id,curp,street,number,colony,cellphone,homephone,email,birthday,referred_by,relative_name,category_id,image) ";
		$sql .= "value (\"$this->name\",\"$this->sex_id\",\"$this->curp\",\"$this->street\",\"$this->number\",\"$this->colony\",\"$this->cellphone\",\"$this->homephone\",\"$this->email\",\"$this->birthday\",\"$this->referred_by\",\"$this->relative_name\",\"$this->category_id\",\"$this->image\")";
		return Executor::doit($sql);
	}

	public function delete(){
		$sql = "DELETE FROM ".self::$tablename." WHERE id = $this->id";
		Executor::doit($sql);
	}

	public function update_active(){
		$sql = "update ".self::$tablename." set last_active_at=NOW() WHERE id=$this->id";
		Executor::doit($sql);
	}

	
	public static function getAllCategories(){
		$sql = "SELECT * FROM ".self::$tablenameCategories."";
		$query = Executor::doit($sql);
		return Model::many($query[0],new PatientData());
	}


	public static function getAllByPage($start_FROM,$limit){
		$sql = "SELECT * FROM ".self::$tablename." WHERE id>=$start_FROM limit $limit";
		$query = Executor::doit($sql);
		return Model::many($query[0],new PatientData());
	}

	public function update(){
		$sql = "update ".self::$tablename." set name=\"$this->name\",curp=\"$this->curp\",relative_name=\"$this->relative_name\",street=\"$this->street\",number=\"$this->number\",colony=\"$this->colony\",cellphone=\"$this->cellphone\",homephone=\"$this->homephone\",email=\"$this->email\",birthday=\"$this->birthday\",referred_by=\"$this->referred_by\",category_id=\"$this->category_id\",relative_name=\"$this->relative_name\",image=\"$this->image\" WHERE id=$this->id";
		return Executor::doit($sql);
	}

	public function updateNotes(){
		$sql = "UPDATE ".self::$tablename." set notes=\"$this->notes\" WHERE id=$this->id";
		return Executor::doit($sql);
	}

	public function updateAssistant($colonia){
		$sql = "update ".self::$tablename." set name=\"$this->name\",curp=\"$this->curp\",relative_name=\"$this->relative_name\",street=\"$this->street\",number=\"$this->number\",colony=\"$this->colony\",cellphone=\"$this->cellphone\",homephone=\"$this->homephone\",email=\"$this->email\",birthday=\"$this->birthday\",referred_by=\"$this->referred_by\" WHERE id=$this->id";
		Executor::doit($sql);
	}

	public static function getById($id){
		$sql = "SELECT *,DATE_FORMAT(birthday,'%d/%m/%Y') AS birthday_format FROM ".self::$tablename." WHERE id = '$id'";
		$query = Executor::doit($sql);
		return Model::one($query[0],new PatientData());
	}

	public static function getByName($name){
		$sql = "SELECT *,DATE_FORMAT(birthday,'%d/%m/%Y') AS birthday_format FROM ".self::$tablename." WHERE name = '$name' LIMIT 1";
		$query = Executor::doit($sql);
		return Model::one($query[0],new PatientData());
	}

	public static function getAll(){
		$sql = "SELECT * FROM ".self::$tablename." order by name";
		$query = Executor::doit($sql);
		return Model::many($query[0],new PatientData());
	}

	public static function getAll_doc(){
		$sql = "SELECT * FROM ".self::$tablename."  WHERE doctor='1'  order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new PatientData());
	}

	public static function getAll_doc_A($id){
		$sql = "SELECT doctor FROM patients WHERE id='$id'";
		$query = Executor::doit($sql);
		return Model::one($query[0],new PatientData());
	}

	public static function getPatientCategories(){
	    $sql = "SELECT * FROM  patient_categories";
	    $query = Executor::doit($sql);
	    return Model::many($query[0],new PatientData());
	}

	public static function get_registro_patientse($name){
	    $sql = "SELECT * FROM `patients` WHERE name='$name'";
	    $query = Executor::doit($sql);
	    return Model::many($query[0],new PatientData());
	}

	public static function getValidatePatientCategory($patient_id,$category_id){
		//Valida si un paciente está en cierta clasificación colocada por la clínica.
		//Por ejemplo buscar si paciente está en lista negra para no darle consulta.
		$sql = "SELECT * FROM ".self::$tablename." WHERE id=\"$patient_id\" and category_id = \"$category_id\"";
		$query = Executor::doit($sql);
		return Model::one($query[0],new ReservationData());
	}

	public static function getAllActive(){
		$sql = "SELECT * FROM client WHERE last_active_at>=date_sub(NOW(),interval 3 second)";
		$query = Executor::doit($sql);
		return Model::many($query[0],new PatientData());
	}

	public static function getAllUnActive(){
		$sql = "SELECT * FROM client WHERE last_active_at<=date_sub(NOW(),interval 3 second)";
		$query = Executor::doit($sql);
		return Model::many($query[0],new PatientData());
	}

	public static function getLike($q){
		$sql = "SELECT * FROM ".self::$tablename." WHERE title like '%$q%' or email like '%$q%'";
		$query = Executor::doit($sql);
		return Model::many($query[0],new PatientData());
	}

	public static function getEmailsByPatientBirthdayMonth($date){
		$sql = "SELECT * FROM ".self::$tablename." WHERE DATE_FORMAT(birthday,'%m-%d') = '$date' AND email != ''";
		$query = Executor::doit($sql);
		return Model::many($query[0],new PatientData());
	}

	/*-----------------FILES-------------------- */
	public function addFile(){
		$sql = "INSERT INTO ".self::$tablenameFiles." (patient_id,reservation_id,path) ";
		$sql .= "VALUE (\"$this->patient_id\",\"$this->reservation_id\",\"$this->path\")";
		return Executor::doit($sql);
	}

	public function deleteFile(){
		$sql = "DELETE FROM ".self::$tablenameFiles." WHERE id = $this->id";
		return Executor::doit($sql);
	}

	public static function getFileById($id){
		$sql = "SELECT * FROM ".self::$tablenameFiles." WHERE id = '$id'";
		$query = Executor::doit($sql);
		return Model::one($query[0],new PatientData());
	}

	public static function getAllFilesByPatientReservation($patientId,$reservationId){
		$sql = "SELECT * FROM ".self::$tablenameFiles." WHERE patient_id = '$patientId' AND reservation_id = '$reservationId'
		ORDER BY created_at DESC";
		$query = Executor::doit($sql);
		return Model::many($query[0],new PatientData());
	}
}

?>