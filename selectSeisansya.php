<?php
$request = isset($_SERVER['HTTP_X_REQUESTED_WITH'])
        ? strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) : '';
if ($request !== 'xmlhttprequest') {
      exit;
}
include_once 'dbconnect.php';
$nensan_id = $_POST['Nensan_id'];

try {
    if ($nensan_id == "") {
        $sql ="SELECT a.Seisansya_ID,a.Seisansya_NAME FROM m_seisansya a,";
        $sql.= " (select seisansya_ID, max(Nensan) max FROM inventoryhead group by Seisansya_ID) b ";
        $sql.= " WHERE a.Seisansya_ID = b.Seisansya_ID";
    } else {
        $sql = "SELECT a.Seisansya_ID,a.Seisansya_NAME FROM m_seisansya a,";
        $sql.= " (select Seisansya_ID FROM inventoryhead Where Nensan=". $nensan_id .") b ";
        $sql.= " WHERE a.Seisansya_ID = b.Seisansya_ID";
    }
    $stmt=$db->query($sql);
} catch (Exception $e) {
    exit('err'.$e->getMessage());
}
$seisansya_list = array();
while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)) {
    $seisansya_list[$row['Seisansya_ID']] = $row['Seisansya_NAME'];
}
header('content-type: application/json; charset=utf-8');
print json_encode($seisansya_list);
