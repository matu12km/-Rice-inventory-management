<?php
$request = isset($_SERVER['HTTP_X_REQUESTED_WITH'])
        ? strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) : '';
if ($request !== 'xmlhttprequest') {
      exit;
}
include_once 'dbconnect.php';
$nensan = $_POST['nensan_id'];
$seisansya_id = $_POST['seisansya_id'];
$hinsyu_id = $_POST['hinsyu_id'];

try {
    $sql = "SELECT Toukyu_ID,Toukyu FROM inventhead_view ";
    $sql .=" WHERE Seisansya_ID ='" .$seisansya_id ."'" ;
    $sql .=" AND Hinsyu_ID ='" . $hinsyu_id . "'";
    $sql .=" AND Nensan ='" . $nensan . "'";
    $stmt=$db->query($sql);
} catch (Exception $e) {
    exit('err'.$e->getMessage());
}
$toukyu_list = array();
while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)) {
    $toukyu_list[$row['Toukyu_ID']] = $row['Toukyu'];
}
header('content-type: application/json; charset=utf-8');
print json_encode($toukyu_list);
