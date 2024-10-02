<?php
class ReservationData
{
	public static $tablename = "reservations";
	public static $tablenameProducts = "reservation_products"; //Tabla para registrar los servicios/ingresos conceptos asociados a la cita
	public static $months = ["01" => "Enero", "02" => "Febrero", "03" => "Marzo", "04" => "Abril", "05" => "Mayo", "06" => "Junio", "07" => "Julio", "08" => "Agosto", "09" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre"];

	public $id;
	public $patient_id;
	public $medic_id;
	public $laboratory_id;
	public $status_id;
	public $created_at;
	public $category_id;
	public $area_id;
	public $date_at;
	public $date_at_final;
	public $user_id;
	public $patient_observations;
	public $diagnostic_observations;
	public $treatment_observations;
	public $physical_observations;
	public $topographical_observations;
	public $reason;
	public $observations_prescription;

	public $product_id;
	public $reservation_id;

	public function __construct()
	{
		$this->created_at = date("Y-m-d H:i:s");
	}

	public function getPatient()
	{
		return PatientData::getById($this->patient_id);
	}
	public function getMedic()
	{
		return MedicData::getById($this->medic_id);
	}
	public function getLaboratory()
	{
		return LaboratoryData::getById($this->laboratory_id);
	}
	public function getStatus()
	{
		return ReservationStatusData::getById($this->status_id);
	}
	public function getCategory()
	{
		return ReservationCategoryData::getById($this->category_id);
	}
	public function getArea()
	{
		return ReservationAreaData::getById($this->area_id);
	}
	public function getNotifications()
	{
		return NotificationData::getByReservationType($this->id);
	}

	public function getReservationDateFormat()
	{
		$day = substr($this->date_at, 8, 2);
		$month = substr($this->date_at, 5, 2);
		$year = substr($this->date_at, 0, 4);

		return $day . "/" . self::$months[$month] . "/" . $year;
	}

	public function getDate()
	{
		$date = substr($this->date_at, 0, 10);
		return $date;
	}

	public function getStartTime()
	{
		$time = substr($this->date_at, 11, 5);
		return $time;
	}

	public function getEndTime()
	{
		$time = substr($this->date_at_final, 11, 5);
		return $time;
	}

	public static function getById($id)
	{
		$sql = "SELECT " . self::$tablename . ".*,
		DATE_FORMAT(date_at,'%d/%m/%Y') AS date_format,
		CONCAT(ELT(WEEKDAY(date_at) + 1, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo')) AS day_name
		FROM " . self::$tablename . " 
		WHERE " . self::$tablename . ".id = '$id'";
		$query = Executor::doit($sql);
		return Model::one($query[0], new ReservationData());
	}

	public function addByPatient()
	{
		$sql = "INSERT INTO " . self::$tablename . " (patient_id,medic_id,date_at,date_at_final,user_id,reason,category_id,area_id,laboratory_id)";
		$sql .= "value (\"$this->patient_id\",\"$this->medic_id\",\"$this->date_at\",\"$this->date_at_final\",\"$this->user_id\",\"$this->reason\",\"$this->category_id\",\"$this->area_id\",\"$this->laboratory_id\")";
		return Executor::doit($sql);
	}

	public function updatePatient()
	{
		$sql = "UPDATE " . self::$tablename . " set patient_id=\"$this->patient_id\",medic_id=\"$this->medic_id\",date_at=\"$this->date_at\",date_at_final=\"$this->date_at_final\",reason=\"$this->reason\",laboratory_id=\"$this->laboratory_id\",category_id=\"$this->category_id\",area_id=\"$this->area_id\" WHERE id=$this->id";
		return Executor::doit($sql);
	}

	public function addDoctor()
	{
		$sql = "INSERT INTO " . self::$tablename . " (medic_id,user_id,date_at,date_at_final,reason)";
		$sql .= "value (\"$this->medic_id\",\"$this->user_id\",\"$this->date_at\",\"$this->date_at_final\",reason=\"$this->reason\")";
		return Executor::doit($sql);
	}

	public function updateDoctor()
	{
		$sql = "UPDATE " . self::$tablename . " set medic_id=\"$this->medic_id\",user_id=\"$this->user_id\",date_at=\"$this->date_at\",date_at_final=\"$this->date_at_final\",reason=\"$this->reason\" WHERE id = $this->id";
		return Executor::doit($sql);
	}

	public function delete()
	{
		$sql = "DELETE FROM " . self::$tablename . " where id = $this->id";
		return Executor::doit($sql);
	}

	public function updateStatus()
	{
		$sql = "UPDATE " . self::$tablename . " set status_id=\"$this->status_id\" WHERE id = $this->id";
		return Executor::doit($sql);
	}

	public function finish()
	{
		$sql = "UPDATE " . self::$tablename . " set date_at_final=\"$this->date_at_final\" WHERE id = $this->id";
		return Executor::doit($sql);
	}

	public function updateReason()
	{
		$sql = "UPDATE " . self::$tablename . " set reason=\"$this->reason\" WHERE id = $this->id";
		return Executor::doit($sql);
	}

	public function updateObservationsPrescription()
	{
		$sql = "UPDATE " . self::$tablename . " set observations_prescription=\"$this->observations_prescription\" WHERE id = $this->id";
		return Executor::doit($sql);
	}

	public function updatePatientObservations()
	{
		$sql = "UPDATE " . self::$tablename . " set patient_observations=\"$this->patient_observations\" WHERE id = $this->id";
		return Executor::doit($sql);
	}

	public function updateDiagnosticObservations()
	{
		$sql = "UPDATE " . self::$tablename . " set diagnostic_observations=\"$this->diagnostic_observations\" WHERE id = $this->id";
		return Executor::doit($sql);
	}

	public function updateTreatmentObservations()
	{
		$sql = "UPDATE " . self::$tablename . " set treatment_observations=\"$this->treatment_observations\" WHERE id = $this->id";
		return Executor::doit($sql);
	}

	public function updatePhysicalObservations()
	{
		$sql = "UPDATE " . self::$tablename . " set physical_observations=\"$this->physical_observations\" WHERE id = $this->id";
		return Executor::doit($sql);
	}

	public function updateTopographicalObservations()
	{
		$sql = "UPDATE " . self::$tablename . " set topographical_observations=\"$this->topographical_observations\" WHERE id = $this->id";
		return Executor::doit($sql);
	}

	public static function get_laboratorio($id)
	{
		$sql = "SELELCT * from laboratorios where id='$id'";
		$query = Executor::doit($sql);
		return Model::many($query[0], new ReservationData());
	}

	public static function getRepeatedReservation($patientId, $medicId, $dateAt, $laboratoryId)
	{
		//Valida si ya se fijó la cita del paciente con ese doctor, ese día y en ese consultorio
		$sql = "SELECT * FROM " . self::$tablename . " WHERE patient_id = '$patientId' AND medic_id = '$medicId' AND date_at=\"$dateAt\" AND laboratory_id=\"$laboratoryId\"";
		$query = Executor::doit($sql);
		return Model::one($query[0], new ReservationData());
	}

	public static function getRepeatedLaboratory($dateAt, $laboratoryId)
	{
		//Valida si el laboratorio/consultorio ya está ocupado en ese horario.
		$sql = "SELECT * FROM " . self::$tablename . " WHERE date_at=\"$dateAt\" AND laboratory_id=\"$laboratoryId\"";
		$query = Executor::doit($sql);
		return Model::one($query[0], new ReservationData());
	}

		public static function getActiveReservationsByMedicDate($dateAt,$dateAtFinal, $medicId)
	{
		//Valida si el laboratorio/consultorio ya está ocupado en ese horario.
		$sql = "SELECT r.*,p.name AS patient_name
		FROM " . self::$tablename . " r 
		INNER JOIN " . PatientData::$tablename . " p ON p.id = r.patient_id
		WHERE  r.medic_id=\"$medicId\" AND (r.date_at BETWEEN \"$dateAt\" AND \"$dateAtFinal\" OR r.date_at_final BETWEEN \"$dateAt\" AND \"$dateAtFinal\")";
		$query = Executor::doit($sql);
		return Model::many($query[0], new ReservationData());
	}

	public static function getByMail($mail)
	{
		$sql = "SELECT * from " . self::$tablename . " where mail=\"$mail\"";
		$query = Executor::doit($sql);
		return Model::one($query[0], new ReservationData());
	}

	public static function getEvery($fecha1)
	{
		$sql = "SELECT  r.clave,r.id,r.pacient_id,r.medic_id,r.date_at,date_at_final,DATE_FORMAT(r.time_at,'%h:%i %p') time_at,r.user_id,r.note,r.status_reser,r.color,r.color_letra,negritas  from reservation r WHERE date_at>='$fecha1' order by date_at ASC";
		$query = Executor::doit($sql);
		return Model::many($query[0], new ReservationData());
	}

	public static function getEvery_doctor($id_user, $fecha1)
	{
		$sql = "SELECT  r.clave,r.id,r.pacient_id,r.medic_id,r.date_at,date_at_final,DATE_FORMAT(r.time_at,'%h:%i %p') time_at,r.user_id,r.note,r.status_reser,r.color,r.color_letra,negritas from reservation r where medic_id='$id_user' AND date_at>='$fecha1' order by date_at ASC";
		$query = Executor::doit($sql);
		return Model::many($query[0], new ReservationData());
	}

	public static function getEvery_au($fecha1)
	{
		$sql = "SELECT  r.clave,r.id,r.pacient_id,r.medic_id,r.date_at,date_at_final,DATE_FORMAT(r.time_at,'%h:%i %p') time_at,r.user_id,r.note,r.status_reser,r.color,r.color_letra,negritas from reservation r where negritas='1' AND date_at>='$fecha1' order by date_at ASC";
		$query = Executor::doit($sql);
		return Model::many($query[0], new ReservationData());
	}

	public static function getAll()
	{
		//NO UTILIZADO
		$sql = "SELECT * from " . self::$tablename . " where date(date_at)>=date(NOW()) order by date_at";
		$query = Executor::doit($sql);
		return Model::many($query[0], new ReservationData());
	}

	public static function getByStartDate($startDate, $medicId = 0)
	{
		//Obtiene TODAS las citas a partir de una fecha
		//Se muestra al administrador y recepcionista
		//El formato de la fecha es datetime
		$sql = "SELECT r.id,r.patient_id,r.medic_id,r.date_at,r.date_at_final,rc.name AS reservation_category_name,
					TRIM(p.name) AS patient_name, TRIM(p.cellphone) AS patient_phone,
					m.name AS medic_name,r.user_id,r.reason,r.category_id,
					m.calendar_color,
					o.id AS sale_id,o.status_id AS sale_status_id
					FROM " . self::$tablename . " r
					INNER JOIN medics m ON r.medic_id = m.id
					LEFT JOIN reservation_categories rc ON r.category_id = rc.id
					LEFT JOIN patients p ON r.patient_id = p.id
					LEFT JOIN " . OperationData::$tablenameReservations . " ore ON r.id = ore.reservation_id 
					AND ore.id = (SELECT MAX(or_sq.id) FROM " . OperationData::$tablenameReservations . " or_sq WHERE or_sq.reservation_id = r.id)
					LEFT JOIN " . OperationData::$tablename . " o ON o.id = ore.operation_id 
					WHERE date_at >= '$startDate' ";
		if ($medicId != 0) {
			$sql .= " AND r.medic_id = '$medicId' ";
		}
		$sql .= " ORDER BY date_at ASC";

		$query = Executor::doit($sql);
		return Model::many($query[0], new ReservationData());
	}

	public static function getBetweenDates($startDateTime, $endDateTime, $medicId = 0, $reservationType = "all", $moduleId = 0, $typeId = 0)
	{
		//Obtiene TODAS las citas entre las fechas seleccionadas
		//Se muestra al administrador y recepcionista
		//El formato de la fecha es datetime
		//$reservationType = "patient" $reservationType = "medic"
		//También se puede filtrar por la cantidad de notificaciones que tiene de cierto módulo o tipo
		$sql = "SELECT r.id,r.patient_id,r.medic_id,r.date_at,date_at_final,rc.name AS reservation_category_name,
					DATE_FORMAT(r.date_at,'%d/%m/%Y %H:%i %p') AS date_at_format,
					TRIM(p.name) AS patient_name, TRIM(p.cellphone) AS patient_phone,
					m.name AS medic_name,r.user_id,r.reason,r.category_id,
					m.calendar_color,
					TRIM(p.email) AS patient_email,
					o.id AS sale_id,o.status_id AS sale_status_id
					FROM " . self::$tablename . " r
					INNER JOIN medics m ON r.medic_id = m.id
					LEFT JOIN reservation_categories rc ON r.category_id = rc.id
					LEFT JOIN patients p ON r.patient_id = p.id
					LEFT JOIN " . OperationData::$tablenameReservations . " ore ON r.id = ore.reservation_id 
					AND ore.id = (SELECT MAX(or_sq.id) FROM " . OperationData::$tablenameReservations . " or_sq WHERE or_sq.reservation_id = r.id)
					LEFT JOIN " . OperationData::$tablename . " o ON o.id = ore.operation_id 
					WHERE date_at >='$startDateTime'
					AND date_at <= '$endDateTime'";
		if ($reservationType == "medic") { //Obtiene las citas solamente de médicos
			$sql .= " AND (r.patient_id IS NULL OR r.patient_id = 0) ";
		} else if ($reservationType == "patient") { //Obtiene las citas solamente de pacientes
			$sql .= " AND (r.patient_id IS NOT NULL OR r.patient_id != 0) ";
		}
		if ($moduleId != 0 && $typeId != 0) { //Obtiene de acuerdo al total de notificaciones por módulo y tipo
			$sql .= " AND (SELECT COUNT(id) FROM " . NotificationData::$tablename . " n 
							WHERE r.id = n.reservation_id
							AND n.module_id = '$moduleId'
							AND n.type_id = '$typeId') = 0 ";
		}
		if ($medicId != 0) {
			$sql .= " AND r.medic_id = '$medicId' ";
		}

		$sql .= " ORDER BY date_at ASC";

		$query = Executor::doit($sql);
		return Model::many($query[0], new ReservationData());
	}

	public static function getByPersonName($name)
	{
		//Obtiene las citas cuando coincida el nombre buscado con el de un médico, paciente o el familiar de un paciente
		$sql = "SELECT CONCAT(ELT(WEEKDAY(r.date_at) + 1, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo')) AS day_name,
			DATE_FORMAT(r.date_at,'%d/%m/%Y %H:%i') AS date_at,r.id,r.patient_id,r.medic_id,r.user_id,
			m.name AS medic_name,p.name AS patient_name,p.cellphone AS patient_phone, p.relative_name AS relative_name
			FROM " . self::$tablename . " r,medics m,patients p 
			WHERE r.medic_id = m.id 
			AND p.id = r.patient_id 
			AND (m.`name` like '%$name%' OR p.`name` like '%$name%' OR p.`relative_name` like '%$name%') 
			ORDER BY r.date_at DESC";
		$query = Executor::doit($sql);
		return Model::many($query[0], new ReservationData());
	}

	public static function getByPatient($patientId, $statusId = 0)
	{
		$sql = "SELECT CONCAT(ELT(WEEKDAY(r.date_at) + 1, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo')) AS day_name,
			DATE_FORMAT(r.date_at,'%d/%m/%Y %H:%i')AS date_at_format,DATE_FORMAT(r.date_at,'%H:%i:%s') AS hour_at,
			rs.name AS status_name,r.status_id,r.id,r.patient_id,r.medic_id,r.user_id,r.reason,
			o.id AS sale_id,o.status_id AS sale_status_id
			FROM " . self::$tablename . " r 
			INNER JOIN " . ReservationStatusData::$tablename . " rs ON rs.id = r.status_id
			LEFT JOIN " . OperationData::$tablenameReservations . " ore ON r.id = ore.reservation_id 
			AND ore.id = (SELECT MAX(or_sq.id) FROM " . OperationData::$tablenameReservations . " or_sq WHERE or_sq.reservation_id = r.id)
			LEFT JOIN " . OperationData::$tablename . " o ON o.id = ore.operation_id 
			WHERE r.patient_id = '$patientId' ";
		if ($statusId != 0) {
			$sql .= " AND r.status_id = '$statusId' ";
		}
		$sql .= " ORDER BY r.date_at DESC ";

		$query = Executor::doit($sql);
		return Model::many($query[0], new ReservationData());
	}

	public static function getAll_filter_reservaciones_doctor($na, $id_user)
	{
		$sql = "SELECT  r.id,r.pacient_id,r.medic_id,r.date_at,r.user_id,r.note,m.name,m.id_usuario  from reservation r,medic m,pacient p where date(date_at)>=date(NOW()) AND r.medic_id=m.id AND p.id=r.pacient_id AND m.id_usuario='$id_user'  AND (m.`name` like '%$na%' OR p.`name` like '%$na%') order by date_at ASC";
		$query = Executor::doit($sql);
		return Model::many($query[0], new ReservationData());
	}

	public static function getAll_reser_doctor()
	{
		$sql = "SELECT  * FROM reservation  WHERE date(date_at)>=date(NOW()) AND id_col=0 order by date_at ASC";
		$query = Executor::doit($sql);
		return Model::many($query[0], new ReservationData());
	}

	public static function getAllPendings()
	{
		$sql = "SELECT * from " . self::$tablename . " WHERE date(date_at)>=date(NOW()) and status_id=1 and payment_id=1 order by date_at";
		$query = Executor::doit($sql);
		return Model::many($query[0], new ReservationData());
	}

	public static function getAllByPacientId($id)
	{
		$sql = "SELECT * from " . self::$tablename . " WHERE pacient_id=$id order by date_at";
		$query = Executor::doit($sql);
		return Model::many($query[0], new ReservationData());
	}

	public static function getAllByMedicId($id)
	{
		$sql = "SELECT * from " . self::$tablename . " WHERE medic_id=$id order by date_at";
		$query = Executor::doit($sql);
		return Model::many($query[0], new ReservationData());
	}

	public static function getLike($q)
	{
		$sql = "SELECT * from " . self::$tablename . " WHERE title like '%$q%'";
		$query = Executor::doit($sql);
		return Model::many($query[0], new ReservationData());
	}

	public static function getReservationCategories()
	{
		$sql = "SELECT id,name,description FROM reservation_categories";
		$query = Executor::doit($sql);
		return Model::many($query[0], new UserData());
	}

	public static function getReservationCategoryById($id)
	{
		$sql = "SELECT id,name,description FROM reservation_categories WHERE id='$id'";
		$query = Executor::doit($sql);
		return Model::one($query[0], new UserData());
	}

	//Obtiene los correos electrónicos de todos los pacientes que tienen citas en un rango de fechas
	public static function getEmailsReservationBetweenDates($startDateTime, $endDateTime, $moduleId, $typeId)
	{
		$sql = "SELECT TRIM(p.email) AS email
					FROM " . self::$tablename . " r
					INNER JOIN patients p ON r.patient_id = p.id
					WHERE date_at >='$startDateTime'
					AND date_at <= '$endDateTime'";
		if ($moduleId != 0 && $typeId != 0) { //Obtiene de acuerdo al total de notificaciones por módulo y tipo
			$sql .= " AND (SELECT COUNT(id) FROM " . NotificationData::$tablename . " n 
										WHERE r.id = n.reservation_id
										AND n.module_id = '$moduleId'
										AND n.type_id = '$typeId') = 0 ";
		}
		$query = Executor::doit($sql);
		return Model::many($query[0], new ReservationData());
	}
	//Obtiene los teléfonos de todos los pacientes que tienen citas en un rango de fechas
	public static function getCellphonesReservationBetweenDates($startDateTime, $endDateTime, $moduleId, $typeId)
	{
		$sql = "SELECT REPLACE(TRIM(p.email),' ','') AS cellphone
					FROM " . self::$tablename . " r
					INNER JOIN patients p ON r.patient_id = p.id
					WHERE date_at >='$startDateTime'
					AND date_at <= '$endDateTime'";
		if ($moduleId != 0 && $typeId != 0) { //Obtiene de acuerdo al total de notificaciones por módulo y tipo
			$sql .= " AND (SELECT COUNT(id) FROM " . NotificationData::$tablename . " n 
										WHERE r.id = n.reservation_id
										AND n.module_id = '$moduleId'
										AND n.type_id = '$typeId') = 0 ";
		}
		$query = Executor::doit($sql);
		return Model::many($query[0], new ReservationData());
	}

	/*--------------PRODUCTOS(CONCEPTOS INGRESOS) EN CITA ------------*/
	public function addProduct()
	{
		$sql = "INSERT INTO " . self::$tablenameProducts . " (product_id,reservation_id)";
		$sql .= "value (\"$this->product_id\",\"$this->reservation_id\")";
		return Executor::doit($sql);
	}

	public static function deleteProductByReservation($reservationId, $productId)
	{
		$sql = "DELETE FROM " . self::$tablenameProducts . " 
		WHERE reservation_id = '$reservationId' AND product_id = '$productId'";
		return Executor::doit($sql);
	}

	public static function deleteAllProductsByReservation($reservationId)
	{
		$sql = "DELETE FROM " . self::$tablenameProducts . " WHERE reservation_id = '$reservationId'";
		return Executor::doit($sql);
	}

	public static function getProductsByTypeReservation($typeId, $reservationId)
	{
		//Obtiene todos los productos disponibles (concepto/ingresos) por tipo y si está seleccionado en la cita
		$sql = "SELECT p.*,rp.id AS reservation_product_id
					FROM " . ProductData::$tablename . " p
					LEFT JOIN " . self::$tablenameProducts . " rp ON rp.product_id = p.id
					AND rp.reservation_id = '$reservationId'
					WHERE p.type_id='$typeId' AND p.is_active = 1 ORDER BY p.name
					";
		$query = Executor::doit($sql);
		return Model::many($query[0], new ReservationData());
	}

	//Obtiene los productos de un tipo específico
	public static function getProductsByReservation($reservationId)
	{
		$sql = "SELECT * FROM " . self::$tablenameProducts . " 
		WHERE reservation_id='$reservationId'";
		$query = Executor::doit($sql);
		return Model::many($query[0], new ProductData());
	}

	public static function getWithoutSaleByPatient($patientId)
	{
		//Obtiene todas las citas de un paciente que no tienen una venta asociada
		$sql = "SELECT CONCAT(ELT(WEEKDAY(r.date_at) + 1, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo')) AS day_name,
			DATE_FORMAT(r.date_at,'%d/%m/%Y %H:%i')AS date_at_format,DATE_FORMAT(r.date_at,'%H:%i:%s') AS hour_at,
			rs.name AS status_name,r.status_id,r.id,r.patient_id,r.medic_id,r.user_id,r.reason
			FROM " . self::$tablename . " r 
			INNER JOIN " . ReservationStatusData::$tablename . " rs ON rs.id = r.status_id
			WHERE r.patient_id = '$patientId' 
			AND (SELECT COUNT(id) FROM " . OperationData::$tablenameReservations . " sr WHERE sr.reservation_id = r.id) = 0
		    ORDER BY r.date_at DESC ";
		$query = Executor::doit($sql);
		return Model::many($query[0], new ReservationData());
	}

	public static function getTotalsByReservation($reservationId)
	{
		//Obtiene todas las citas de un paciente que no tienen una venta asociada
		$sql = "SELECT COUNT(rp.id) as quantity_products, SUM(p.price_out) AS total_products
			FROM " . self::$tablenameProducts . " rp 
			INNER JOIN " . ProductData::$tablename . " p ON rp.product_id = p.id
			WHERE rp.reservation_id = '$reservationId'
			LIMIT 1";
		$query = Executor::doit($sql);
		return Model::one($query[0], new ReservationData());
	}
}

/*
	public static function getByStartDateMedic($startDate, $medicId)
	{
		//Obtiene las citas de un médico a partir de la fecha seleccionada
		//El formato de la fecha es datetime
		$sql = "SELECT r.id,r.patient_id,r.medic_id,r.date_at,date_at_final,rc.name as reservation_category_name,
				TRIM(p.name) AS patient_name, TRIM(p.cellphone) AS patient_phone,
				TRIM(m.name) AS medic_name,r.user_id,r.reason,r.category_id,
				m.calendar_color
				FROM " . self::$tablename . " r
				INNER JOIN medics m ON r.medic_id = m.id
				LEFT JOIN reservation_categories rc ON r.category_id = rc.id
				LEFT JOIN patients p ON r.patient_id = p.id
				WHERE r.medic_id = '$medicId' 
				AND date_at>='$startDate' 
				ORDER BY date_at ASC";
		$query = Executor::doit($sql);
		return Model::many($query[0], new ReservationData());
	}*/

		/*public static function getBetweenDatesMedic($startDateTime, $endDateTime, $medicId)
	{
		//Obtiene las citas de un médico entre las fechas seleccionadas
		//El formato de la fecha es datetime
		$sql = "SELECT r.id,r.patient_id,r.medic_id,r.date_at,date_at_final,rc.name as reservation_category_name,
				TRIM(p.name) AS patient_name, TRIM(p.cellphone) AS patient_phone,
				TRIM(m.name) AS medic_name,r.user_id,r.reason,r.category_id,
				m.calendar_color
				FROM " . self::$tablename . " r
				INNER JOIN medics m ON r.medic_id = m.id
				LEFT JOIN reservation_categories rc ON r.category_id = rc.id
				LEFT JOIN patients p ON r.patient_id = p.id
				WHERE r.medic_id = '$medicId' 
				AND date_at >= '$startDateTime' 
				AND date_at <= '$endDateTime' 
				ORDER BY date_at ASC";
		$query = Executor::doit($sql);
		return Model::many($query[0], new ReservationData());
	}*/