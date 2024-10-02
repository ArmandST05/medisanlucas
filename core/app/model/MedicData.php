<?php
class MedicData {
	public static $tablename = "medics";
	public $id;
	public $category_id;
	public $user_id;
	public $name;
	public $email;
	public $phone;
	public $study_center;
	public $study_center_logo;
	public $is_study_center_prescription;
	public $calendar_color;
	public $professional_license;
	public $is_digital_signature;
	public $is_fiel_key;
	public $digital_signature_path;
	public $fiel_key_path;
	public $fiel_key_password;
	public $fiel_certificate_path;
	public $other_specialties;
	public $created_at;

	public function __construct(){
		$this->created_at = "NOW()";
	}

	//public function getUnreads(){ return MessageData::getUnreadsByClientId($this->id); }

	public function getCategory(){ 
		return CategoryMedicData::getById($this->category_id); 
	}

	public function getUser(){ 
		return UserData::getById($this->user_id); 
	}

	public function add(){
		$sql = "insert into ".self::$tablename." (category_id,name,email,phone,professional_license,study_center,study_center_logo,is_study_center_prescription,user_id,calendar_color,other_specialties) ";
		$sql .= "value ($this->category_id,\"$this->name\",\"$this->email\",\"$this->phone\",\"$this->professional_license\",\"$this->study_center\",\"$this->study_center_logo\",\"$this->is_study_center_prescription\",\"$this->user_id\",\"$this->calendar_color\",\"$this->other_specialties\")";
		return Executor::doit($sql);
	}

	public function update_active(){
		$sql = "update ".self::$tablename." set last_active_at=NOW() WHERE id=$this->id";
		Executor::doit($sql);
	}

	public function update(){
		$sql = "update ".self::$tablename." set category_id=$this->category_id,name=\"$this->name\",professional_license=\"$this->professional_license\",study_center=\"$this->study_center\",study_center_logo=\"$this->study_center_logo\",is_study_center_prescription=\"$this->is_study_center_prescription\",email=\"$this->email\",phone=\"$this->phone\",user_id=\"$this->user_id\",calendar_color=\"$this->calendar_color\",other_specialties=\"$this->other_specialties\" WHERE id = $this->id";
		Executor::doit($sql);
	}
	
	public function updateProfile(){
		//Utilizado para que el médico edite sus datos
		$sql = "UPDATE ".self::$tablename." SET category_id=$this->category_id,name=\"$this->name\",professional_license=\"$this->professional_license\",study_center=\"$this->study_center\",email=\"$this->email\",phone=\"$this->phone\",is_study_center_prescription=\"$this->is_study_center_prescription\",is_digital_signature=\"$this->is_digital_signature\",is_fiel_key=\"$this->is_fiel_key\",other_specialties=\"$this->other_specialties\" WHERE id = $this->id";
		Executor::doit($sql);
	}

	public function updateDigitalSignature(){
		//Utilizado para actualizar la firma digital
		$sql = "UPDATE ".self::$tablename." SET digital_signature_path=\"$this->digital_signature_path\" WHERE id = $this->id";
		Executor::doit($sql);
	}
	
	public function updateFielKey(){
		//Utilizado para actualizar la clave fiel (SAT)
		$sql = "UPDATE ".self::$tablename." SET fiel_key_path=\"$this->fiel_key_path\" WHERE id = $this->id";
		Executor::doit($sql);
	}

	public function updateFielKeyPassword(){
		//Utilizado para actualizar la contraseña de la clave fiel (SAT)
		$sql = "UPDATE ".self::$tablename." SET fiel_key_password=\"$this->fiel_key_password\" WHERE id = $this->id";
		Executor::doit($sql);
	}
	
	public function updateFielCertificate(){
		//Utilizado para actualizar el certificado de fiel (SAT)
		$sql = "UPDATE ".self::$tablename." SET fiel_certificate_path=\"$this->fiel_certificate_path\" WHERE id = $this->id";
		Executor::doit($sql);
	}

	public function updateStudyCenterLogo(){
		//Utilizado para actualizar el logo del centro de estudios
		$sql = "UPDATE ".self::$tablename." SET study_center_logo=\"$this->study_center_logo\" WHERE id = $this->id";
		return Executor::doit($sql);
	}

	public static function getById($id){
		$sql = "SELECT * FROM ".self::$tablename." WHERE id='$id'";
		$query = Executor::doit($sql);
		return Model::one($query[0],new MedicData());
	}

	public static function getByUserId($id){
		$sql = "SELECT * FROM ".self::$tablename." WHERE user_id = '$id' LIMIT 1";
		$query = Executor::doit($sql);
		return Model::one($query[0],new MedicData());
	}

    public static function getAll_pa(){
		$nom = "";
		$sql = "SELECT * FROM ".self::$tablename." WHERE name like '%$nom%' order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new PatientData());
	}

	public static function getAll_med($id){
	    $sql = "SELECT * FROM medic WHERE id='$id'";
	    $query = Executor::doit($sql);
	    return Model::many($query[0],new PatientData());
	}

	public static function getAll_med_user($id){
	    $sql = "SELECT * FROM ".self::$tablename." WHERE id_usuario='$id'";
	    $query = Executor::doit($sql);
	    return Model::many($query[0],new PatientData());
	}

	public static function getAll(){
		$sql = "SELECT * FROM ".self::$tablename." order by name";
		$query = Executor::doit($sql);
		return Model::many($query[0],new MedicData());
	}

	public static function getAllActive(){
		$sql = "SELECT * FROM client WHERE last_active_at>=date_sub(NOW(),interval 3 second)";
		$query = Executor::doit($sql);
		return Model::many($query[0],new MedicData());
	}

	public static function getAllUnActive(){
		$sql = "SELECT * FROM client WHERE last_active_at<=date_sub(NOW(),interval 3 second)";
		$query = Executor::doit($sql);
		return Model::many($query[0],new MedicData());
	}

	public static function getLike($q){
		$sql = "SELECT * FROM ".self::$tablename." WHERE title like '%$q%' or email like '%$q%'";
		$query = Executor::doit($sql);
		return Model::many($query[0],new MedicData());
	}

	public static function delById($id){
		$sql = "delete FROM ".self::$tablename." WHERE id=$id";
		Executor::doit($sql);
	}
	public function delete(){
		$sql = "DELETE FROM ".self::$tablename." WHERE id=$this->id";
		return Executor::doit($sql);
	}
}

?>