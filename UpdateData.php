<?php
function ChangeData($params, $db)
{
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    //No
    $no = $params['No'];
    //入出荷日
    $InOutDate = $params['UpInOutDate'];
    //入荷数
    $ArrivalQuantity = $params['UpArrivalQuantity'];
    if ($ArrivalQuantity == 0) {
        $ArrivalQuantity = null;
    }
    //出荷数
    $ShipmentQuantity = $params['UpShipmentQuantity'];
    if ($ShipmentQuantity == 0) {
        $ShipmentQuantity = null;
    }
    //備考
    $bikou = trim($params['UpBikou']);
    //更新日
    $UpDateDate = Date('Y-m-d H:i:s');
    //更新者
    $UpDateUser = $_SESSION['user'];
    try {
        $sql = " UPDATE inventorydata";
        $sql.= " SET";
        $sql.= "     InOutDate =:InOutDate,";
        $sql.= "     ArrivalQuantity =:ArrivalQuantity,";
        $sql.= "     ShipmentQuantity =:ShipmentQuantity,";
        $sql.= "     bikou =:bikou,";
        $sql.= "     UpDateDate =:UpDateDate,";
        $sql.= "     UpDateUser =:UpDateUser";
        $sql.= " WHERE No =:no";

        $stmt = $db->prepare($sql);
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
        //更新日
        $stmt->bindvalue(':UpDateDate', $UpDateDate, PDO::PARAM_STR);
        //更新者
        $stmt->bindvalue(':UpDateUser', $UpDateUser, PDO::PARAM_STR);
        //No
        $stmt->bindvalue(':no', $no, PDO::PARAM_INT);
        $stmt->execute();
    } catch (PDOException $e) {
        echo $e->getMessage();
        die();
        return "false";
    }
    return "success";
}
