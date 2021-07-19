<?php
ob_start();
session_start();
if (isset($_SESSION['user']) != "") {
    header("Location: home.php");
}
include_once 'dbconnect.php';
try {
    // ログインボタンがクリックされたときに下記を実行
    if (isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // クエリの実行
        $stmt = $db->prepare('SELECT * FROM users WHERE username=?');
        $stmt->execute(array($username));

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (password_verify($password, $row['password'])) {
                session_regenerate_id(true);
                $id = $row['id'];
                $sql = "SELECT * FROM users WHERE id = $id";
                $stmt = $db->query($sql);
                foreach ($stmt as $row) {
                    $row['username'];
                }
                $_SESSION['user'] = $row['username'];
                header("Location: home.php");
                exit();
            } else {
                $alertMessage = "ユーザー名とパスワードが一致しません。";
            }
        } else {
                $alertMessage = "ユーザー名とパスワードが一致しません。";
        }
    }
} catch (PDOException $e) {
    $php_errormsg = "データベースエラー";
}
if (isset($alertMessage)) {
    $alert = "<script type='text/javascript'>window.onload = function() { alert('". $alertMessage. "'); }</script>";
    echo $alert;
}
?>
<!doctype html>
<html lang="ja" >
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>ログイン</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="./style/BasicStyle.css"/>
    <link rel="stylesheet" href="./style/signin.css"/>
  </head>
  <body class="text-center">
    <form method="post" class="form-signin">
      <img class="mb-4" src="./img/wakatochi_logo.svg" alt="" width="72" height="72"/>
      <h1 class="h3 mb-3 font-weight-normal">ログイン</h1>
      <label for="inputName" class="sr-only">ユーザー名</label>
      <input type="username" id="inputName" class="form-control" name="username" placeholder="ユーザー名" required autofocus>
      <label for="inputPassword" class="sr-only">パスワード</label>
      <input type="password" id="inputPassword" class="form-control" name="password" placeholder="パスワード" required/>
      <button type="submit" class="btn btn-lg btn-primary btn-block mb-3" name="login">ログイン</button>
      <?php
        if ($_SERVER['REMOTE_ADDR'] == $ip) {
              echo '<a href="register.php">登録はこちら</a>';
        }?>
    </form>

    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
      integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n"
      crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
      integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
      crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
      integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6"
      crossorigin="anonymous"></script>
  </body>
</html>
