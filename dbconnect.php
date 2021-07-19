<?php
require_once('./core/config.php');
$dsn = 'mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8;';

try {
    $db = new PDO($dsn, $username, $password);
} catch (PDOException $e) {
    error_log($db->connect_error);
    exit('データーベース接続失敗' . $e->getMessage());
}
  