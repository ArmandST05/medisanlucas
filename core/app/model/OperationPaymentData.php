<?php
class OperationPaymentData
{
	public static $tablename = "operation_payments";

	public function __construct(){
		$this->name = "";
		$this->product_id = "";
		$this->quantity = "";
		$this->cut_id = "";
		$this->operation_type_id = "";
		$this->created_at = "NOW()";
	}

	public function getType(){ return PaymentTypeData::getById($this->payment_type_id);}

	/*SE OBTIENE SI ES INGRESOS (1) O EGRESOS(2) POR LA CATEGORÍA DE LA OPERACIÓN */
	public function add()
	{
		$sql = "INSERT INTO ".self::$tablename." (payment_type_id,operation_id,total,date) ";
		$sql .= "value ($this->payment_type_id,$this->operation_id,$this->total,\"$this->date\")";
		return Executor::doit($sql);
	}

	public function addU()
	{
		$sql = "INSERT INTO " . self::$tablename . " (product_id,q,operation_type_id,operation_id,created_at,price) ";
		$sql .= "value (\"$this->product_id\",\"$this->q\",$this->operation_type_id,$this->operation_id,$this->date,$this->price)";
		return Executor::doit($sql);
	}

	public function delete()
	{
		$sql = "DELETE FROM ".self::$tablename." WHERE id=$this->id";
		return Executor::doit($sql);
	}
	
	public static function deleteByOperationId($operationId){
		$sql = "DELETE FROM ".self::$tablename."  WHERE operation_id = $operationId";
		return Executor::doit($sql);
	}

	public static function getTotalByOperationId($operationId)
	{
		//Obtener el total pagado por operación (gastos-ventas,etc)
		$sql = "SELECT SUM(total) total FROM ".self::$tablename."
		WHERE operation_id = '$operationId'";
		
		$query = Executor::doit($sql);
		return Model::one($query[0], new OperationPaymentData());
	}

	
	public static function getById($id)
	{
		$sql = "SELECT * FROM ".self::$tablename." WHERE id='$id'";
		$query = Executor::doit($sql);
		return Model::one($query[0], new OperationPaymentData());
	}
	
	public static function getAllByOperationId($operationId)
	{
		$sql = "SELECT * FROM ".self::$tablename." 
		WHERE operation_id='$operationId'";
		$query = Executor::doit($sql);
		return Model::many($query[0], new OperationPaymentData());
	}

}
