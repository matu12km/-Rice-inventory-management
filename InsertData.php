<?php
function setData($params, $db, $inventory_ID)
{
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    //入出荷日
    $InOutDate = $params['NewInOutDate'];
    //入荷数
    $ArrivalQuantity = $params['NewArrivalQuantity'];
    if ($ArrivalQuantity == 0) {
        $ArrivalQuantity = null;
    }
    //出荷数
    $ShipmentQuantity = $params['NewShipmentQuantity'];
    if ($ShipmentQuantity == 0) {
        $ShipmentQuantity = null;
    }
    //備考
    $bikou = trim($params['NewBikou']);
    //登録日
    $InsertDate = Date('Y-m-d H:i:s');
    //登録者
    $InsertUser = $_SESSION['user'];
    try {
        $sql = " INSERT INTO inventorydata";
        $sql.= " (Inventory_ID, InOutDate, ArrivalQuantity, ShipmentQuantity, bikou, InsertDate, InsertUser)";
        $sql.= " VALUES(:inventory_ID, :InOutDate, :ArrivalQuantity, ";
        $sql.= "        :ShipmentQuantity, :bikou, :InsertDate, :InsertUser)";

        $stmt = $db->prepare($sql);
        //インベントリーID
        $stmt->bindvalue(':inventory_ID', $inventory_ID, PDO::PARAM_INT);
        //入出荷日
        $stmt->bindvalue(':InOutDate', $InOutDate, PDO::PARAM_STR);
        //入荷数
        if (!empty($ArrivalQuantity)) {
            $stmt->bindvalue(':ArrivalQuantity', $ArrivalQuantity, PDO::PARAM_INT);
        } else {
            $stmt->bindvalue(':ArrivalQuantity', $ArrivalQuantity, PDO::PARAM_NULL);
        }
        //出荷数
        if (!empty($ShipmentQuantity)) {
            $stmt->bindvalue(':ShipmentQuantity', $ShipmentQuantity, PDO::PARAM_INT);
        } else {
            $stmt->bindvalue(':ShipmentQuantity', $ShipmentQuantity, PDO::PARAM_NULL);
        }
        //備考
        if (!empty($bikou)) {
            $stmt->bindvalue(':bikou', $bikou, PDO::PARAM_STR);
        } else {
            $stmt->bindvalue(':bikou', $bikou, PDO::PARAM_NULL);
        }
        //登録日
        $stmt->bindvalue(':InsertDate', $InsertDate, PDO::PARAM_STR);
        //登録者
        $stmt->bindvalue(':InsertUser', $InsertUser, PDO::PARAM_STR);
        $stmt->execute();
    } catch (PDOException $e) {
        echo $e->getMessage();
        die();
        return "false";
    }
    return "success";
}
