<?php
class OperationDetailData
{
    public static $tablename = "operation_details";

    public function __construct()
    {
        $this->name = "";
        $this->product_id = "";
        $this->quantity = "";
        $this->cut_id = "";
        $this->operation_type_id = "";
        $this->created_at = "NOW()";
    }

    public function getProduct()
    {
        return ProductData::getById($this->product_id);
    }
    public function getOperationType()
    {
        return OperationTypeData::getById($this->operation_type_id);
    }

    public function add()
    {
        $sql = "INSERT INTO " . self::$tablename . " (product_id,quantity,operation_type_id,operation_id,created_at,price,reservation_id) ";
        $sql .= "value (\"$this->product_id\",\"$this->quantity\",$this->operation_type_id,$this->operation_id,\"$this->date\",$this->price,\"$this->reservation_id\")";
        return Executor::doit($sql);
    }

    public function delete()
    {
        $sql = "DELETE FROM " . self::$tablename . " WHERE id = $this->id";
        return Executor::doit($sql);
    }

    public static function deleteByOperationId($operationId)
    {
        $sql = "DELETE FROM " . self::$tablename . " WHERE operation_id = $operationId";
        return Executor::doit($sql);
    }

    public static function getById($id)
    {
        $sql = "SELECT * FROM " . self::$tablename . " WHERE id = $id";
        $query = Executor::doit($sql);
        return Model::one($query[0], new OperationDetailData());
    }

    /*--------ENTRADAS---- */
    public function addInput()
    {
        $sql = "INSERT INTO " . self::$tablename . " (product_id,quantity,operation_type_id,expiration_date,lot) ";
        $sql .= "value ($this->product_id,$this->quantity,1,\"$this->expiration_date\",\"$this->lot\")";
        return Executor::doit($sql);
    }

    /*OBTENER POR OPERACIÓN (VENTA,GASTO,ETC) */
    public static function getAllByOperationId($id)
    {
        $sql = "SELECT * FROM " . self::$tablename . " WHERE operation_id='$id'";
        $query = Executor::doit($sql);
        return Model::many($query[0], new OperationDetailData());
    }

    /*---------INVENTARIO ------ */
    public static function getAllByProductId($productId)
    {
        //Obtiene todas las operaciones realizadas de cierto producto
        $sql = "SELECT id,product_id,quantity,price,operation_type_id,operation_id,
            DATE_FORMAT(created_at,'%d-%m-%Y')date,lot,
            created_at,expiration_date,DATE_FORMAT(expiration_date,'%d-%m-%Y')expiration_date_format  
            FROM " . self::$tablename . " WHERE product_id = $productId 
            ORDER BY created_at desc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new OperationDetailData());
    }

    public static function getByOperationTypeProduct($productId, $operationType)
    {
        //Obtiene el total por tipo de operación y producto.
        //Ejemplo: Total de salidas/entradas de producto x
        $sql = "SELECT SUM(quantity) AS total 
            FROM " . self::$tablename . " 
            WHERE product_id = '$productId' 
            AND operation_type_id = '$operationType'";

        $query = Executor::doit($sql);
        $total =  Model::one($query[0], new OperationDetailData())->total;
        return $total;
    }

    public static function getStockByProduct($productId)
    {
        //Obtiene el stock disponible de cierto producto para las ventas
        $stock = 0;
        $totalInputs = self::getByOperationTypeProduct($productId, 1);

        $totalOutputs = self::getByOperationTypeProduct($productId, 2);

        if ($totalInputs > 0) $stock = $totalInputs - $totalOutputs;

        return $stock;
    }
    /*---------INVENTARIO ------ */

    /*-----EXPENSES/PURCHASES--- */
    public function addExpense()
    {
        $sql = "INSERT INTO " . self::$tablename . " (product_id,quantity,operation_type_id,operation_id,created_at,price,expiration_date) ";
        $sql .= "value (\"$this->product_id\",\"$this->quantity\",$this->operation_type_id,$this->operation_id,\"$this->date\",$this->price,\"$this->expiration_date\")";
        return Executor::doit($sql);
    }

    public static function updateDate($operationId, $date)
    {
        $sql = "UPDATE " . self::$tablename . " SET created_at = '$date' WHERE operation_id='$operationId'";
        return Executor::doit($sql);
    }

    public static function updateTotalStatus($operationId, $total, $statusId)
    {
        $sql = "UPDATE " . self::$tablename . " SET total='$total',status_id='$statusId' WHERE id = '$operationId'";
        return Executor::doit($sql);
    }

    public function update()
    {
        $sql = "UPDATE " . self::$tablename . " set product_id=\"$this->product_id\",quantity=\"$this->quantity\" WHERE id = $this->id";
        Executor::doit($sql);
    }

    public static function getAll()
    {
        $sql = "SELECT * FROM " . self::$tablename;
        $query = Executor::doit($sql);
        return Model::many($query[0], new OperationDetailData());
    }

    //Obtiene el total de entradas de productos para el reporte del inventario agrupadas por fecha de caducidad
    public static function getAllExpirationDatesByProduct($productId)
    {
        $sql = "SELECT product_id,SUM(quantity) quantity,lot,
                DATE_FORMAT(expiration_date,'%d/%m/%Y') AS expiration_date,
                DATE_FORMAT(expiration_date,'%m') AS month,
                DATE_FORMAT(expiration_date,'%Y')AS expiration_year, 
                DATE_FORMAT(expiration_date,'%Y%m') AS expiration_year_month,
                timestampdiff(month,curdate(),expiration_date) difference_month 
                FROM " . self::$tablename . " 
                WHERE  operation_type_id='1' AND product_id = '$productId'
                GROUP BY product_id,expiration_date 
                ORDER BY expiration_year_month ASC";
        $query = Executor::doit($sql);
        return Model::many($query[0], new OperationDetailData());
    }

    //Obtiene el total de salidas de productos para el reporte del inventario
    public static function getTotalOutputsByProduct($productId)
    {
        $sql = "SELECT product_id,SUM(quantity) quantity 
            FROM " . self::$tablename . " 
            WHERE  operation_type_id='2' 
            AND product_id = '$productId' 
            GROUP BY product_id";
        $query = Executor::doit($sql);
        return Model::one($query[0], new OperationDetailData());
    }

    public static function getAllProductsByOperationId($operationId)
    {
        $sql = "SELECT * FROM " . self::$tablename . " 
        WHERE operation_id = $operationId ORDER BY created_at DESC";
        $query = Executor::doit($sql);
        return Model::many($query[0], new OperationDetailData());
    }

    public static function getOutputByProductId($product_id)
    {
        $sql = "SELECT * FROM " . self::$tablename . " WHERE product_id=$product_id and operation_type_id=2 order by created_at desc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new OperationDetailData());
    }

    public static function getInputByProductId($product_id)
    {
        $sql = "SELECT * FROM " . self::$tablename . " WHERE product_id=$product_id and operation_type_id=1 order by created_at desc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new OperationDetailData());
    }

    public static function getSalesByDatesProductType($startDate, $endDate, $productTypeId)
    {
        $startDateTime = $startDate . " 00:00:01";
        $endDateTime = $endDate . " 23:59:59";
        //Obtiene todas las ventas generadas en ciertas fechas por cada producto
        $sql = "SELECT p.barcode,p.name as product_name,
            (SELECT SUM(od.quantity)FROM " . self::$tablename . " od
            WHERE od.operation_type_id = 2 AND od.product_id = p.id
            AND created_at >= '$startDateTime'
            AND created_at <= '$endDateTime') as quantity,
            (SELECT SUM(od.quantity * od.price) FROM " . self::$tablename . " od
            WHERE od.operation_type_id = 2 AND od.product_id = p.id
            AND created_at >= '$startDateTime'
            AND created_at <= '$endDateTime') as total  
            FROM   " . ProductData::$tablename . " p 
            WHERE p.type_id = '$productTypeId'";
        $query = Executor::doit($sql);
        return Model::many($query[0], new OperationData());
    }

    /*-------RESERVATIONS----*/
    public static function getBySaleReservation($saleId, $reservationId)
    {
        //Obtiene los productos/conceptos de una venta filtrado por la cita
        $sql = "SELECT * FROM " . self::$tablename . " 
            WHERE operation_id='$saleId'
            AND reservation_id='$reservationId'";

        $query = Executor::doit($sql);
        return Model::many($query[0], new OperationDetailData());
    }

    public static function getAllProductsByReservation($reservationId)
    {
        //Obtiene todos los productos vendidos asociados a una cita (pueden ser de distintas ventas)
        $sql = "SELECT od.* FROM " . self::$tablename . " od 
        WHERE od.reservation_id='$reservationId'";
        $query = Executor::doit($sql);
        return Model::many($query[0], new OperationData());
    }

    /*
    public static function getAllByDateOfficial($start, $end)
    {
        $sql = "SELECT * FROM " . self::$tablename . " WHERE date(created_at) >= \"$start\" and date(created_at) <= \"$end\" order by created_at desc";
        if ($start == $end) {
            $sql = "SELECT * FROM " . self::$tablename . " WHERE date(created_at) = \"$start\" order by created_at desc";
        }
        $query = Executor::doit($sql);
        return Model::many($query[0], new OperationDetailData());
    }

    public static function getAllByProductIdCutId($product_id, $cut_id)
    {
        $sql = "SELECT * FROM " . self::$tablename . " WHERE product_id=$product_id and cut_id=$cut_id order by created_at desc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new OperationDetailData());
    }

    public static function getInventarioPro()
    {
        $sql = "SELECT  id, name, type,minimum_inventory FROM product WHERE type='MEDICAMENTO'";
        $query = Executor::doit($sql);
        return Model::many($query[0], new OperationDetailData());
    }

    public static function getAllByProductIdFechas($product_id, $f2)
    {
        $sql = "SELECT * FROM " . self::$tablename . " WHERE product_id=$product_id  AND DATE_FORMAT(created_at,'%Y-%m-%d') >= '2019-04-01' AND  DATE_FORMAT(created_at,'%Y-%m-%d') <= '$f2'";
        $query = Executor::doit($sql);
        return Model::many($query[0], new OperationDetailData());
    }

    public static function getAllByProductIdCutIdOficial($product_id, $cut_id)
    {
        $sql = "SELECT * FROM " . self::$tablename . " WHERE product_id=$product_id and cut_id=$cut_id order by created_at desc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new OperationDetailData());
    }

    public static function getOutputByProductIdCutId($product_id, $cut_id)
    {
        $sql = "SELECT * FROM " . self::$tablename . " WHERE product_id=$product_id and cut_id=$cut_id and operation_type_id=2 order by created_at desc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new OperationDetailData());
    }

        public static function getInputByProductIdCutId($product_id, $cut_id)
    {
        $sql = "SELECT * FROM " . self::$tablename . " WHERE product_id=$product_id and cut_id=$cut_id and operation_type_id=1 order by created_at desc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new OperationDetailData());
    }

        public static function getInputByProductIdCutIdYesF($product_id, $cut_id)
    {
        $sql = "SELECT * FROM " . self::$tablename . " WHERE product_id=$product_id and cut_id=$cut_id and operation_type_id=1 order by created_at desc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new OperationDetailData());
    }

        public static function getOutputQ($product_id, $cut_id)
    {
        $q = 0;
        $operations = self::getOutputByProductIdCutId($product_id, $cut_id);
        $input_id = OperationTypeData::getByName("entrada")->id;
        $output_id = OperationTypeData::getByName("salida")->id;
        foreach ($operations as $operation) {
            if ($operation->operation_type_id == $input_id) {
                $q += $operation->q;
            } else if ($operation->operation_type_id == $output_id) {
                $q += (-$operation->q);
            }
        }
        return $q;
    }

    public static function getAllByDateOfficialBP($product, $start, $end)
    {
        $sql = "SELECT * FROM " . self::$tablename . " WHERE date(created_at) >= \"$start\" and date(created_at) <= \"$end\" and product_id=$product order by created_at desc";
        if ($start == $end) {
            $sql = "SELECT * FROM " . self::$tablename . " WHERE date(created_at) = \"$start\" order by created_at desc";
        }
        $query = Executor::doit($sql);
        return Model::many($query[0], new OperationDetailData());
    }

    public static function getQYesFechass($product_id)
    {
        $q = 0;
        $operations = self::getAllByProductId($product_id);
        $input_id = OperationTypeData::getByName("entrada")->id;
        $output_id = OperationTypeData::getByName("salida")->id;
        foreach ($operations as $operation) {
            if ($operation->operation_type_id == $input_id) {
                $q += $operation->q;
            } else if ($operation->operation_type_id == $output_id) {
                $q += (-$operation->q);
            }
        }
        return $q;
    }

    public static function getQYesFechas($product_id, $f2)
    {
        $q = 0;
        $operations = self::getAllByProductIdFechas($product_id, $f2);
        $input_id = OperationTypeData::getByName("entrada")->id;
        $output_id = OperationTypeData::getByName("salida")->id;
        foreach ($operations as $operation) {
            if ($operation->operation_type_id == $input_id) {
                $q += $operation->q;
            } else if ($operation->operation_type_id == $output_id) {
                $q += (-$operation->q);
            }
        }
        return $q;
    }
        public static function getOutputQYesF($product_id)
    {
        $q = 0;
        $operations = self::getOutputByProductId($product_id);
        $input_id = OperationTypeData::getByName("entrada")->id;
        $output_id = OperationTypeData::getByName("salida")->id;
        foreach ($operations as $operation) {
            if ($operation->operation_type_id == $input_id) {
                $q += $operation->q;
            } else if ($operation->operation_type_id == $output_id) {
                $q += (-$operation->q);
            }
        }
        // print_r($data);
        return $q;
    }

    public static function getInputQYesF($product_id)
    {
        $q = 0;
        $operations = self::getInputByProductId($product_id);
        $input_id = OperationTypeData::getByName("entrada")->id;
        foreach ($operations as $operation) {
            if ($operation->operation_type_id == $input_id) {
                $q += $operation->q;
            }
        }
        // print_r($data);
        return $q;
    }
*/
}
