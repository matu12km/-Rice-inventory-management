<?php
$request = isset($_SERVER['HTTP_X_REQUESTED_WITH'])
        ? strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) : '';
if ($request !== 'xmlhttprequest') {
      exit;
}
include_once 'dbconnect.php';
$nensan = $_POST['nensan_id'];
$seisansya_id ="";
//if(!empty($_POST['seisansya_id'])){
    $seisansya_id = $_POST['seisansya_id'];
//}

try {
    $sql = "SELECT Hinsyu_ID,Hinsyu_NAME";
    $sql.= " FROM inventhead_view ";
    $sql.= " WHERE Nensan ='" . $nensan . "'";
    //if($seisansya_id<>""){
        $sql.= "  AND Seisansya_ID ='" . $seisansya_id . "'";
    //}

    $stmt=$db->query($sql);
} catch (Exception $e) {
    exit('err'.$e->getMessage());
}
$Hinsyu_list = array();
while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)) {
    $Hinsyu_list[$row['Hinsyu_ID']] = $row['Hinsyu_NAME'];
}

header('content-type: application/json; charset=utf-8');
print json_encode($Hinsyu_list);
