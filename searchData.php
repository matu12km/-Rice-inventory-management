<?php
function getData($params, $db)
{
    $db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
    $result = [];
    $where = [];
    $where[] = 'Nensan = "' .$params['Nensan'].'"';
    $_SESSION['parmNensan'] = $params['Nensan'];
    if(isset($params['Seisansya']) && $params['Seisansya']<>""){
        $where[] = 'Seisansya_ID = "' .$params['Seisansya'].'"';
        $_SESSION['parmSeisansya'] = $params['Seisansya'];
    }else{
        $_SESSION['parmSeisansya'] = NULL;
    }
    if(isset($params['Toukyu']) && $params['Toukyu']<>""){
        $where[] = 'Toukyu_ID = "' .$params['Toukyu'].'"';
        $_SESSION['parmToukyu'] =$params['Toukyu'];
    }else{
        $_SESSION['parmToukyu'] =NULL;
    }
    if(isset($params['Hinsyu'])){
        $where[] = 'Hinsyu_ID = "'.$params['Hinsyu'].'"';
        $_SESSION['parmHinsyu'] =$params['Hinsyu'];
    }else{
        $_SESSION['parmHinsyu'] =NULL;
    }
    if(count($where)>1){
        $whereSql = implode(' AND ', $where);
    }else{
        $whereSql = implode('', $where);
    }

    $sql = "SELECT d.Inventory_ID, d.Seisansya_ID, d.Seisansya_NAME,";
    $sql.= " d.Toukyu_ID,d.Toukyu, d.Hinsyu_ID, d.Hinsyu_NAME, d.Nensan, a.*";
    $sql.= " FROM inventorydata a,";
    $sql.= "  (SELECT Inventory_ID, Seisansya_ID, Seisansya_NAME, Toukyu_ID, Toukyu,";
    $sql.= " Hinsyu_ID, Hinsyu_NAME,Nensan FROM inventhead_view b";
    $sql.= "     WHERE " . $whereSql . ") d";
    $sql.= " WHERE a.Inventory_ID = d.Inventory_ID";
    $sql.= " AND not (ArrivalQuantity Is null AND ShipmentQuantity Is null)";
    $sql.= " ORDER BY InOutDate, No";
    try {
        $stmt = $db->query($sql);
    } catch (Exception $e) {
        echo("searchData 2" .$e->getMessage() . "  SQL" . $sql);
    }
    $count = $stmt->rowcount();
    if ($count == 0) {
        try {
            $sql2 = "SELECT Inventory_ID, Seisansya_ID, Seisansya_NAME, Toukyu_ID,Toukyu, Hinsyu_ID,Hinsyu_NAME,Nensan";
            $sql2.= " FROM inventhead_view";
            $sql2.= " WHERE " . $whereSql;
            $stmt2 = $db->query($sql2);
            while ($row2 = $stmt2 -> fetch(PDO::FETCH_ASSOC)) {
                $result[] = $row2;
            }
        } catch (PDOException $e) {
            echo ($e);
        }
    } else {
        while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)) {
            $result[] = $row;
        }
    }
    return $result;
}
