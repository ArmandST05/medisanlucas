<?php
class NotificationData
{
	public static $tablename = "notifications";
	public static $tablenameModules = "notification_modules";
	public static $tablenameStatus = "notification_status";
	public static $tablenameDirections = "notification_directions";
	public static $tablenameTypes = "notification_types";
	public static $tablenameSmsPurchases = "sms_purchases";
	public static $tablenameDefaultMessages = "default_messages";

	public $patient_id;
	public $reservation_id;
	public $type_id;
	public $direction_id;
	public $status_id;
	public $module_id;
	public $receptor;
	public $message;

	//Tipos de notificaciones: CORREO ELECTRÓNICO (1), SMS (2)
	public function __construct()
	{
		$this->patient_id = "";
		$this->reservation_id = "";
		$this->type_id = "";
		$this->direction_id = "";
		$this->status_id = "";
		$this->module_id = "";
		$this->receptor = "";
		$this->message = "";
	}

	public function add()
	{
		$sql = "INSERT INTO " . self::$tablename . " (patient_id,reservation_id,type_id,direction_id,status_id,module_id,receptor,message) ";
		$sql .= "value (\"$this->patient_id\",\"$this->reservation_id\",\"$this->type_id\",\"$this->direction_id\",$this->status_id,\"$this->module_id\",\"$this->receptor\",\"$this->message\")";
		Executor::doit($sql);
	}

	public static function getById($id)
	{
		$sql = "SELECT * FROM " . self::$tablename . " WHERE id = '$id'";
		$query = Executor::doit($sql);
		return Model::one($query[0], new NotificationData());
	}

	public static function getAllByType($typeId)
	{
		$sql = "SELECT * FROM " . self::$tablename . " WHERE type_id = '$typeId' ORDER BY date_at ASC";
		$query = Executor::doit($sql);
		return Model::many($query[0], new NotificationData());
	}
	//Obtiene el detalle de todas las notificaciones enviadas en una cita, se puede filtrar por tipo de notificación(medio).
	public static function getByReservationType($reservationId, $typeId = 0)
	{
		$sql = "SELECT * FROM " . self::$tablename . " 
				WHERE reservation_id = '$reservationId' ";
		if ($typeId != 0) {
			$sql .= "AND type_id = '$typeId' ";
		}
		$sql .= " ORDER BY date_at ASC ";
		$query = Executor::doit($sql);
		return Model::many($query[0], new NotificationData());
	}

	//Obtiene el total de notificaciones sobre una cita específica de acuerdo al tipo de notificación
	public static function getTotalByReservationType($reservationId, $typeId = 0)
	{
		//Correo(1),SMS(2),Whatsapp(3)
		$sql = "SELECT COUNT(id) AS total FROM " . self::$tablename . " 
				WHERE reservation_id = '$reservationId' ";
		if ($typeId != 0) {
			$sql .= "AND type_id = '$typeId' ";
		}
		$query = Executor::doit($sql);
		return Model::one($query[0], new NotificationData());
	}

	//Obtiene el total de notificaciones a un paciente de acuerdo al tipo de notificación
	public static function getTotalByPatientType($patientId, $typeId = 0)
	{
		$sql = "SELECT COUNT(id) AS total FROM " . self::$tablename . " 
				WHERE patient_id = '$patientId' ";
		if ($typeId != 0) {
			$sql .= "AND type_id = '$typeId' ";
		}
		$query = Executor::doit($sql);
		return Model::one($query[0], new NotificationData());
	}

	public static function getAllByPatientType($patientId, $typeId = 0)
	{
		$sql = "SELECT " . self::$tablename . ".*,DATE_FORMAT(date,'%d %b %y %h:%i %p') AS date_chat_format 
		FROM " . self::$tablename . " 
			WHERE patient_id = '$patientId' ";
			if ($typeId != 0) {
				$sql .= " AND type_id = '$typeId' ";
			}
		$sql .= " ORDER BY date ASC ";
		$query = Executor::doit($sql);
		return Model::many($query[0], new NotificationData());
	}

	public static function getTotalByType($typeId)
	{
		$sql = "SELECT COUNT(id) AS total FROM " . self::$tablename . " WHERE type_id = '$typeId'";
		$query = Executor::doit($sql);
		return Model::one($query[0], new NotificationData());
	}

	public static function getTotalByDatesType($typeId, $startDate, $endDate)
	{
		$startDateTime = $startDate . " 00:00:01";
		$endDateTime = $endDate . " 23:59:59";
		$sql = "SELECT COUNT(id) AS total FROM " . self::$tablename . " 
		WHERE type_id = '$typeId'
		AND date >= '$startDateTime'
		AND date <= '$endDateTime' ";
		$query = Executor::doit($sql);
		return Model::one($query[0], new NotificationData());
	}

	//Obtiene el total de sms comprados/cargados al sistema para el uso del cliente
	public static function getTotalSmsPurchases()
	{
		$sql = "SELECT SUM(quantity) AS total FROM " . self::$tablenameSmsPurchases . " ";
		$query = Executor::doit($sql);
		return Model::one($query[0], new NotificationData());
	}

	public static function getAvailableSms()
	{
		//Total available
		$purchasedSmsData = self::getTotalSmsPurchases();
		$usedSmsData = self::getTotalByType(2);
		$totalAvailable = ($purchasedSmsData->total - $usedSmsData->total);

		//Available days
		$configuration = ConfigurationData::getAll();
		$startDate = date("Y-m-d", strtotime(date('Y-m-d') . "- " . ($configuration["notifications_sms_days_to_calculate_available"]->value) . " days"));
		$endDate = date("Y-m-d");

		$totalNotifications = self::getTotalByDatesType(2, $startDate, $endDate);
		$averageSendSms = $totalNotifications->total / $configuration["notifications_sms_days_to_calculate_available"]->value;
		$averageSendSms = ($averageSendSms == 0) ? 100 : $averageSendSms;
		$availableDays = round(($totalAvailable / $averageSendSms),0, PHP_ROUND_HALF_DOWN);

		if ($availableDays < 7 && $availableDays > 3) {
			$availableClass = "warning";
		} elseif ($availableDays < 3) {
			$availableClass = "danger";
		} else {
			$availableClass = "info";
		}

		$availableData = [];
		$availableData["total"] = $totalAvailable;
		$availableData["days"] = $availableDays;
		$availableData["class"] = $availableClass;
		return $availableData;
	}

	public static function getResumeByReservation($reservationId)
	{
		$notificationsData = [];
		$notificationsData["total"] = self::getTotalByReservationType($reservationId)->total;
		$notificationsData["email"] = self::getTotalByReservationType($reservationId, 1)->total;
		$notificationsData["sms"] = self::getTotalByReservationType($reservationId, 2)->total;
		$notificationsData["whatsapp"] = self::getTotalByReservationType($reservationId, 3)->total;
		$notificationsData["class"] = "";
		if ($notificationsData["total"] > 0) {
			$notificationsData["class"] = "success";
		}
		return $notificationsData;
	}

	public static function getResumeByPatient($patientId)
	{
		$notificationsData = [];
		$notificationsData["total"] = self::getTotalByPatientType($patientId)->total;
		$notificationsData["email"] = self::getTotalByPatientType($patientId, 1)->total;
		$notificationsData["sms"] = self::getTotalByPatientType($patientId, 2)->total;
		$notificationsData["whatsapp"] = self::getTotalByPatientType($patientId, 3)->total;
		$notificationsData["class"] = "";
		if ($notificationsData["total"] > 0) {
			$notificationsData["class"] = "success";
		}
		return $notificationsData;
	}

	public static function getDefaultMessageByModuleType($moduleId, $typeId)
	{
		$sql = "SELECT * FROM " . self::$tablenameDefaultMessages . " 
			WHERE module_id = '$moduleId' AND type_id = '$typeId' LIMIT 1";
		$query = Executor::doit($sql);
		return Model::one($query[0], new NotificationData());
	}
}
