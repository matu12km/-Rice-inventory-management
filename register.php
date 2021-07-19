<?php
session_start();
if (isset($_SESSION['user']) != "") {
    // ログイン済みの場合はリダイレクト
    header("Location: home.php");
}
// DBとの接続
include_once 'dbconnect.php';

if (isset($_POST['signup'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    try {
        $checkSql = "SELECT * FROM users WHERE username = '$username'";
        $result = $db->query($checkSql);
        //入力されたユーザー名で検索し結果が0件なら登録処理に進む
        if ($result->rowCount() == 0) {
            $stmt = $db->prepare("INSERT INTO users(username,password) VALUES(?,?)");
            $stmt->execute(array($username,password_hash($password, PASSWORD_DEFAULT)));
            $alertMessage = "登録しました";
        } else {
            $alertMessage = "入力されてたユーザー名はすでに使われています。";
        }
    } catch (PDOException $e) {
            $alertMessage = "エラーが発生しました。";
    }
}
if (isset($alertMessage)) {
    $alert = "<script type='text/javascript'>window.onload = function() { alert('". $alertMessage. "'); }</script>";
    echo $alert;
}

?>
<!DOCTYPE HTML>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>ユーザー登録</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="./style/BasicStyle.css">
    <link rel="stylesheet" href="./style/signin.css">
  </head>
  <body class="text-center">
    <form method="post" class="form-signin">
      <img class="mb-4" src="./img/wakatochi_logo.svg" alt="" width="72" height="72"/>
      <h1 class="h3 mb-3 font-weight-normal">ユーザー登録</h1>
      <label for="inputName" class="sr-only">ユーザー名</label>
      <input type="username" id="inputName" class="form-control" name="username" placeholder="ユーザー名" required autofocus>
      <label for="inputPassword" class="sr-only">パスワード</label>
      <input type="password" id="inputPassword" class="form-control" name="password" placeholder="パスワード" required/>
      <button type="submit" class="btn btn-lg btn-primary btn-block mb-3" name="signup">登録する</button>
      <a href="index.php">ログインはこちら</a>
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
