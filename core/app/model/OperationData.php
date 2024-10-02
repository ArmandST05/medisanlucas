<?php
class OperationData
{
	public static $tablename = "operations";
	public static $tablenameReservations = "operation_reservations";

	public $operation_id;
	public $reservation_id;
	public $created_at;
	public $discount;
	public $discount_percentage;

	public function __construct()
	{
		$this->created_at = "NOW()";
	}

	public function getUser()
	{
		return UserData::getById($this->user_id);
	}
	public function getPatient()
	{
		return PatientData::getById($this->patient_id);
	}
	public function getMedic()
	{
		return MedicData::getById($this->medic_id);
	}

	public function add()
	{
		$sql = "INSERT INTO " . self::$tablename . " (total,discount,user_id,created_at) ";
		$sql .= "value ($this->total,$this->discount,$this->user_id,$this->created_at)";
		return Executor::doit($sql);
	}

	public function addOutput()
	{
		$sql = "INSERT INTO " . self::$tablename . " (user_id,operation_type_id,created_at,total,operation_category_id,description) ";
		$sql .= "value ($this->user_id,1,$this->created_at,$this->total,'2',\"$this->description\")";
		return Executor::doit($sql);
	}

	public static function getDescription($id)
	{
		$sql = "SELECT description FROM " . self::$tablename . " WHERE id='$id'";
		$query = Executor::doit($sql);
		return Model::one($query[0], new OperationData());
	}

	public function updateDescription()
	{
		$sql = "UPDATE  " . self::$tablename . " set description=\"$this->description\" WHERE id = $this->id";
		return Executor::doit($sql);
	}

	public function updateDate()
	{
		$sql = "UPDATE " . self::$tablename . " SET created_at = '$this->created_at' WHERE id='$this->id'";
		Executor::doit($sql);
	}

	public function updateDiscount()
	{
		$sql = "UPDATE " . self::$tablename . " SET discount = '$this->discount',discount_percentage = '$this->discount_percentage' 
		WHERE id='$this->id'";
		return Executor::doit($sql);
	}

	public function updateTotal()
	{
		$sql = "UPDATE " . self::$tablename . " SET total=$this->total WHERE id='$this->id'";
		return Executor::doit($sql);
	}

	public function updateStatus()
	{
		$sql = "UPDATE " . self::$tablename . " SET status_id = '$this->status_id' WHERE id = '$this->id'";
		return Executor::doit($sql);
	}

	public function updateIsInvoice()
	{
		$sql = "UPDATE " . self::$tablename . " SET is_invoice='$this->value' WHERE id='$this->id'";
		return Executor::doit($sql);
	}

	public function updateInvoiceNumber()
	{
		$sql = "UPDATE " . self::$tablename . " SET invoice_number='$this->invoice_number' WHERE id='$this->id'";
		return Executor::doit($sql);
	}

	public function updateBank()
	{
		$sql = "UPDATE " . self::$tablename . " SET bank='$this->bank' WHERE id='$this->id'";
		Executor::doit($sql);
	}

	public function updateExpense()
	{
		$sql = "UPDATE  " . self::$tablename . " set status_id=$this->status_id,total=$this->total,description=\"$this->description\" WHERE id=$this->id";
		Executor::doit($sql);
	}

	public function delete()
	{
		$sql = "DELETE FROM " . self::$tablename . " WHERE id=$this->id";
		Executor::doit($sql);
	}

	public static function getById($id)
	{
		$sql = "SELECT * FROM " . self::$tablename . " where id=$id";
		$query = Executor::doit($sql);
		return Model::one($query[0], new OperationData());
	}

	public static function getAllByTypeId($categoryId)
	{
		$sql = "SELECT id,created_at,description FROM " . self::$tablename . " WHERE operation_category_id = '$categoryId' ORDER BY created_at DESC";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}

	public static function getAllByPage($start_FROM, $limit)
	{
		$sql = "SELECT * FROM " . self::$tablename . " where id<=$start_FROM limit $limit";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}

	/*-----------EXPENSES/PURCHASES----------- */
	public function addExpense()
	{
		$sql = "INSERT INTO  " . self::$tablename . " (total,user_id,status_id,operation_type_id,operation_category_id,description) ";
		$sql .= "value ($this->total,$this->user_id,$this->status_id,1,3,\"$this->description\")";
		return Executor::doit($sql);
	}

	public static function getAllExpensesByDates($startDate, $endDate)
	{
		$startDateTime = $startDate . " 00:00:00";
		$endDateTime = $endDate . " 23:59:59";

		$sql = "SELECT product_id,quantity,price 
			FROM " . OperationDetailData::$tablename . " o,operations s 
			WHERE o.operation_type_id = '1'
			AND s.status_id = '1' 
			AND o.created_at >= '$startDateTime' 
			AND o.created_at <= '$endDateTime'
			AND s.id = o.operation_id ";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}

	/*-------------------SALES---------------*/
	public function addSale($patientId)
	{
		$sql = "INSERT INTO " . self::$tablename . " (total,discount,discount_percentage,user_id,created_at,patient_id,status_id,description,operation_type_id,operation_category_id)";
		$sql .= "VALUE ($this->total,$this->discount,$this->discount_percentage,$this->user_id,\"$this->date\",'$patientId',$this->status_id,\"$this->description\",2,1)";
		return Executor::doit($sql);
	}

	public static function getAccountStatusByPatient($id)
	{
		$sql = "SELECT id,total,DATE_FORMAT(created_at,'%d/%m/%Y') AS date_format,patient_id,status_id,description,
		CONCAT(ELT(WEEKDAY(created_at) + 1, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo')) AS day_name 
		FROM " . self::$tablename . " 
		WHERE patient_id = '$id' 
		ORDER BY id ASC";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}

	public static function getAllSalesByDates($startDate, $endDate, $paymentTypeId, $statusId = "all", $productId = "all")
	{
		$startDateTime = $startDate . " 00:00:00";
		$endDateTime = $endDate . " 23:59:59";

		//Obtiene todas las ventas generadas en cierta fecha. No importa si están liquidadas o no.
		$sql = "SELECT s.status_id,s.description,s.bank,s.invoice_number,s.id,
			s.total,s.created_at,s.is_invoice,p.name AS patient_name,
			CONCAT(ELT(WEEKDAY(s.created_at) + 1, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo')) AS day_name, 
			DATE_FORMAT(s.created_at,'%d/%m/%Y') AS date_format  
			FROM  " . self::$tablename . " s, patients p 
			WHERE p.id = s.patient_id 
			AND (s.created_at >= '$startDateTime' AND s.created_at <= '$endDateTime')
			AND operation_type_id = '2' ";
		if ($paymentTypeId != 0) {
			$sql .= " AND (SELECT opp.id FROM " . OperationPaymentData::$tablename . " opp 
				WHERE opp.operation_id = s.id 
				AND opp.payment_type_id = '$paymentTypeId' LIMIT 1) 
				IS NOT NULL ";
		}
		if ($productId != "all") {
			$sql .= " AND (SELECT odd.id FROM " . OperationDetailData::$tablename . " odd 
				WHERE odd.operation_id = s.id 
				AND odd.product_id = '$productId' LIMIT 1) 
				IS NOT NULL ";
		}

		if ($statusId != "all") {
			$sql .= " AND s.status_id = '$statusId' ";
		}
		$sql .= " ORDER BY s.id";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}


	public static function getAllSelledProductsByDate($startDate, $endDate)
	{
		$startDateTime = $startDate . " 00:00:00";
		$endDateTime = $endDate . " 23:59:59";

		//Obtiene el total vendido de los productos por día
		$sql = "SELECT product_id,SUM(quantity) quantity,SUM(price) price,SUM(price*quantity) total,patient_id,s.status_id 
		FROM " . OperationDetailData::$tablename . " o,operations s 
		WHERE o.operation_type_id = '2' 
		AND s.status_id = '1' 
		AND o.created_at >= '$startDateTime'
		AND o.created_at <= '$endDateTime'
		AND s.id = o.operation_id 
		GROUP BY product_id";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}

	public static function getAllMedicSalesByProductDate($productId, $date)
	{
		//Obtiene todos los médicos que han vendido cierto producto 
		//en cierta fecha y la cantidad de producto vendida y la ganancia generada por médico.
		//Las ventas deben de estar liquidadas para mostrarlas.
		$sql = "SELECT od.product_id,SUM(od.quantity)quantity,SUM(od.price) price,s.patient_id,r.medic_id 
		FROM " . OperationDetailData::$tablename . " od
		INNER JOIN " . self::$tablename . " s ON s.id = od.operation_id 
		LEFT JOIN " . ReservationData::$tablename . " r ON r.id = od.reservation_id 
		WHERE od.operation_type_id='2' 
		AND od.created_at LIKE '$date%' 
		AND od.product_id='$productId' AND s.status_id='1' 
		GROUP BY od.product_id,r.medic_id";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}

	public static function getAllProductSalesByDate($date)
	{
		//Obtiene los totales vendidos por medicamentos en cierta fecha
		$sql = "SELECT product_id,SUM(o.quantity)quantity,(o.price)price,
				SUM(o.price*o.quantity)total
                FROM " . OperationDetailData::$tablename . " o, products p, " . self::$tablename . " s 
				WHERE o.operation_type_id='2' 
                AND o.created_at like '$date%' 
				AND p.id = o.product_id 
				AND p.type_id = 4
                AND s.`status_id`='1' AND  s.id = o.operation_id 
				GROUP BY o.product_id";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}

	public static function getBySaleStatusPatient($status_id, $patient_id)
	{
		$sql = "SELECT * FROM " . self::$tablename . " 
		WHERE `status_id`='$status_id' AND patient_id='$patient_id'";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}

	/*-----------INGRESOS/INPUTS--------- */

	public static function getInputs($paymentTypeId, $date)
	{
		$sql = "SELECT p.total total,s.bank,s.is_invoice 
		FROM operation_payments p," . self::$tablename . " s  
		WHERE  p.payment_type_id = '$paymentTypeId' 
		AND s.id = p.operation_id 
		AND s.operation_category_id='1' 
		AND date like '$date%' ";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}

	public static function getInputsSales($paymentTypeId, $startDate, $endDate)
	{
		$startDateTime = $startDate . " 00:00:00";
		$endDateTime = $endDate . " 23:59:59";

		$sql = "SELECT p.total total,s.bank,s.is_invoice 
				FROM " . OperationPaymentData::$tablename . " p," . self::$tablename . " s  
				WHERE  p.payment_type_id = '$paymentTypeId' 
				AND s.id = p.operation_id 
				AND s.operation_category_id='1' 
				AND (date >= '$startDateTime' AND date <= '$endDateTime')";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}


	/*-----------SALIDAS/OUTPUTS------------ */
	public static function getOutputs($paymentTypeId, $date)
	{
		$sql = "SELECT p.total total,s.bank,s.is_invoice 
		FROM operation_payments p," . self::$tablename . " s  
		WHERE  p.payment_type_id = '$paymentTypeId'
		AND s.id = p.operation_id
		AND s.operation_category_id = '3' 
		AND date like '$date%' ";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}

	public static function getOutputsExpenses($paymentTypeId, $startDate, $endDate)
	{
		$startDateTime = $startDate . " 00:00:00";
		$endDateTime = $endDate . " 23:59:59";

		$sql = "SELECT p.total total,s.bank,s.is_invoice 
			FROM " . OperationPaymentData::$tablename . " p," . self::$tablename . " s  
			WHERE  p.payment_type_id = '$paymentTypeId'
				AND s.id = p.operation_id
				AND s.operation_category_id = '3' 
				AND (date >= '$startDateTime' AND date <= '$endDateTime')";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}
	/*-----------SALIDAS/OUTPUTS FIN------------ */


	/*-----------VENTAS ASOCIADAS A CITAS----------- */
	public function addSaleReservation()
	{
		$sql = "INSERT INTO  " . self::$tablenameReservations . " (operation_id,reservation_id) ";
		$sql .= "VALUE ($this->operation_id,$this->reservation_id)";
		return Executor::doit($sql);
	}


	public static function deleteSaleReservation($saleId, $reservationId)
	{
		$sql = "DELETE FROM " . self::$tablenameReservations . "  
		WHERE operation_id = '$saleId' AND reservation_id = '$reservationId'";
		return Executor::doit($sql);
	}

	public static function deleteReservationsBySaleId($saleId)
	{
		$sql = "DELETE FROM " . self::$tablenameReservations . "  WHERE operation_id = $saleId";
		return Executor::doit($sql);
	}

	public static function getReservationsBySale($saleId)
	{
		$sql = "SELECT ore.* FROM " . self::$tablenameReservations . " ore
		INNER JOIN " . ReservationData::$tablename . " r ON r.id = ore.reservation_id
		WHERE ore.operation_id = '$saleId'";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}

	public static function getMedicsBySale($saleId)
	{
		//Obtener los médicos vinculados a la citas que están vinculadas a la venta
		$sql = "SELECT m.*
			FROM " . MedicData::$tablename . " m
			INNER JOIN " . ReservationData::$tablename . " r ON r.medic_id = m.id 
			INNER JOIN " . self::$tablenameReservations . " ore ON ore.reservation_id = r.id
			AND ore.operation_id = '$saleId'
			GROUP BY m.id";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}

	public static function getMedicsBySaleString($saleId)
	{
		//Obtener los médicos vinculados a la citas que están vinculadas a la venta
		$medics = self::getMedicsBySale($saleId);
		$medicsString = "";
		foreach($medics as $medic){
			$medicsString .= ", ".$medic->name;
		}
		if($medicsString){
			$medicsString = substr($medicsString, 2);
		}
		return $medicsString;
	}

	/*
	public static function getSells()
	{
		$sql = "SELECT * FROM " . self::$tablename . " where operation_type_id=2 order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}

	public static function getSellsUnBoxed()
	{
		$sql = "SELECT * FROM " . self::$tablename . " where operation_type_id=2 and box_id is NULL order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}

	public function update_box()
	{
		$sql = "update " . self::$tablename . " set box_id=$this->box_id where id=$this->id";
		Executor::doit($sql);
	}

	public function add_re()
	{
		$sql = "INSERT INTO " . self::$tablename . " (user_id,operation_type_id,created_at,total) ";
		$sql .= "value ($this->user_id,1,$this->created_at,$this->total)";
		return Executor::doit($sql);
	}

	public static function getdetMed($f1, $f2)
	{
		$sql = "SELECT DATE_FORMAT(o.created_at,'%Y-%m-%d'),product_id,SUM(q)q,SUM(o.price)price,SUM(o.price*o.q)total
                FROM operation o, product p,  " . self::$tablename . " s WHERE o.operation_type_id='2' 
                AND DATE_FORMAT(o.created_at,'%Y-%m-%d') >= '$f1' AND  DATE_FORMAT(o.created_at,'%Y-%m-%d') <= '$f2'
                AND p.id=o.product_id AND p.idCat=8
                AND s.`status`='1' AND  s.id=o.sell_id GROUP BY o.product_id,p.idCat";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}

	public static function getByBoxId($id)
	{
		$sql = "SELECT * FROM " . self::$tablename . " where operation_type_id=2 and box_id=$id order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}

	public static function getAllByDateOp($start, $end, $op)
	{
		$sql = "SELECT * FROM " . self::$tablename . " where date(created_at) >= \"$start\" and date(created_at) <= \"$end\" and operation_type_id=$op order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}
	
	public static function getAllByDateBCOp($clientid, $start, $end, $op)
	{
		$sql = "SELECT * FROM " . self::$tablename . " where date(created_at) >= \"$start\" and date(created_at) <= \"$end\" and client_id=$clientid  and operation_type_id=$op order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}

	public static function getCatePro($id)
	{
		$sql = "SELECT idCat FROM product WHERE id='$id'";
		$query = Executor::doit($sql);
		return Model::one($query[0], new OperationData());
	}
	
	public static function getTypepayment()
	{
		$sql = "SELECT * FROM typepayment";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}
	
	public static function getNameCat($id)
	{
		$sql = "SELECT name FROM category_spend WHERE id='$id'";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}

	public static function getAllSellFechasP($f1, $f2)
	{
		$sql = "SELECT s.status,s.description,s.banco,s.noFac,s.id,s.total,
		s.created_at,s.fac,p.`name`,CONCAT(ELT(WEEKDAY(s.created_at) + 1, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo')) AS nombre_dia, DATE_FORMAT(s.created_at,'%d/%m/%Y')as fecha  
		FROM  " . self::$tablename . " s, patients p WHERE p.id=s.patient_id 
		AND DATE_FORMAT(s.created_at,'%Y-%m-%d') >= '$f1' 
		AND  DATE_FORMAT(s.created_at,'%Y-%m-%d') <= '$f2' AND s.status=0  
		AND operation_type_id='2' order by id DESC";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}

	public static function getAllExpFechasP($f1, $f2)
	{
		$sql = "SELECT s.status,s.description,s.banco,s.noFac,s.id,s.total,s.created_at,s.fac,CONCAT(ELT(WEEKDAY(s.created_at) + 1, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo')) AS nombre_dia, DATE_FORMAT(s.created_at,'%d/%m/%Y')as fecha  FROM  " . self::$tablename . " s WHERE  DATE_FORMAT(s.created_at,'%Y-%m-%d') >= '$f1' AND  DATE_FORMAT(s.created_at,'%Y-%m-%d') <= '$f2' AND s.status=0  AND operation_type_id='1' order by id DESC";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}

	public static function getAllSellFechas($f1, $f2)
	{
		$sql = "SELECT s.status,s.description,s.banco,s.noFac,s.id,s.total,s.created_at,s.fac,p.`name`,CONCAT(ELT(WEEKDAY(s.created_at) + 1, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo')) AS nombre_dia, 
		DATE_FORMAT(s.created_at,'%d/%m/%Y')as fecha 
		FROM  " . self::$tablename . " s, patients p 
		WHERE p.id = s.patient_id 
		AND DATE_FORMAT(s.created_at,'%Y-%m-%d') >= '$f1' 
		AND DATE_FORMAT(s.created_at,'%Y-%m-%d') <= '$f2' 
		AND s.status = 1 AND operation_type_id = '2' ORDER BY id DESC";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}

	public static function getAllExpFechas($f1, $f2)
	{
		$sql = "SELECT s.status,s.description,s.banco,s.noFac,s.id,s.total,s.created_at,s.fac,CONCAT(ELT(WEEKDAY(s.created_at) + 1, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo')) AS nombre_dia, DATE_FORMAT(s.created_at,'%d/%m/%Y')as fecha  FROM  " . self::$tablename . " s WHERE  DATE_FORMAT(s.created_at,'%Y-%m-%d') >= '$f1' AND  DATE_FORMAT(s.created_at,'%Y-%m-%d') <= '$f2' AND s.status=1 AND operation_type_id='1' order by id DESC";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}
	
	public static function getAllMedicSalesByProductDateUt($id, $f1, $f2)
	{
		$sql = "SELECT product_id,SUM(q)q,SUM(price)price,patient_id,idMedic FROM operation o,sell s WHERE o.operation_type_id='2' AND DATE_FORMAT(o.created_at,'%Y-%m-%d') >= '$f1' AND  DATE_FORMAT(o.created_at,'%Y-%m-%d') <= '$f2'   AND s.status='1' AND s.id=o.sell_id AND product_id='$id' GROUP BY product_id,idMedic";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}

	public static function getIngresosUt($id, $f1, $f2)
	{
		$sql = "SELECT p.total total,s.banco,s.fac FROM pay p,sell s  WHERE  p.idTypePay='$id' AND s.id=p.idSell AND typePay='INGRESOS' AND DATE_FORMAT(date,'%Y-%m-%d') >= '$f1' AND  DATE_FORMAT(date ,'%Y-%m-%d') <= '$f2'  ";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}

	public static function getEgresosEUt($id, $f1, $f2)
	{
		$sql = "SELECT p.total total,s.banco,s.fac FROM pay p,sell s  WHERE  p.idTypePay='$id' AND s.id=p.idSell AND typePay='EGRESOS' AND DATE_FORMAT(date,'%Y-%m-%d') >= '$f1' AND  DATE_FORMAT(date ,'%Y-%m-%d') <= '$f2' ";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}

	public static function getAllSellDateUt($fecha, $fecha2)
	{
		$sql = "SELECT product_id,SUM(q)q,SUM(price)price,patient_id,idMedic,s.status FROM operation o,sell s WHERE o.operation_type_id='2' AND s.status='1' AND DATE_FORMAT(o.created_at,'%Y-%m-%d') >= '$fecha' AND  DATE_FORMAT(o.created_at,'%Y-%m-%d') <= '$fecha2' AND s.id=o.sell_id  GROUP BY product_id";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}

	public static function getAllByPageS($start_FROM, $limit)
	{
		$sql = "SELECT s.id,s.total,s.created_at,s.fac,p.`name` FROM  " . self::$tablename . " s, patients p WHERE p.id=s.patient_id  AND s.id>=$start_FROM limit $limit";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationData());
	}

	*/
}
