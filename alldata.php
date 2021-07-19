<?php
session_start();
//セッションユーザーがセットされていなければ(ログインしていないので)index.phpに飛ばす
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}
require_once 'dbconnect.php';
require_once './common/warekichange.php';
//年産の取得
$sql = "SELECT Nensan FROM inventoryhead ORDER BY Nensan desc";
$stmt = $db->query($sql);
$NensanList = array();
while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)) {
    $NensanList[$row['Nensan']] = wareki($row['Nensan']);
}
//生産者リストの取得
$sql = "SELECT Seisansya_ID,Seisansya_NAME FROM m_seisansya ";
$stmt = $db->query($sql);
$SeisansyaList = array();
while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)) {
    $SeisansyaList[$row['Seisansya_ID']] = $row['Seisansya_NAME'];
}
//等級リストの取得
$sql = "SELECT * FROM m_toukyu";
$stmt = $db->query($sql);
$ToukyuList = array();
while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)) {
    $ToukyuList[$row['Toukyu_ID']] = $row['Toukyu'];
}
//品種リストの取得
$sql = "SELECT * FROM m_hinsyu";
$stmt = $db->query($sql);
$HinsyuList = array();
while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)) {
    $HinsyuList[$row['Hinsyu_ID']] = $row['Hinsyu_NAME'];
}
//基準年をセット
//現在日時が(新米シーズン(10月)より前は前年にする)
if (date('m') < 10) {
    $kijyun = date('Y')-1;
} else {
    $kijyun = date('Y');
}
include_once('./common/YearList.php');
$yearList = getYearList($kijyun);

//検索ボタンを押した時
    include_once('searchData.php');
if (isset($_POST['search'])) {
    $inventoryData = getData($_POST, $db);
    if(count($inventoryData)>0){
    //セッションに保存しておく
        $_SESSION['Inventory_ID'] = htmlspecialchars($inventoryData[0]['Inventory_ID']);
        $_SESSION['Seisansya'] = htmlspecialchars($inventoryData[0]['Seisansya_ID']);
        $_SESSION['Toukyu'] = htmlspecialchars($inventoryData[0]['Toukyu_ID']);
        $_SESSION['Hinsyu'] = htmlspecialchars($inventoryData[0]['Hinsyu_ID']);
        $_SESSION['Nensan'] = htmlspecialchars($inventoryData[0]['Nensan']);
    }
}

?>

<!DOCTYPE html>
<html lang="ja" >
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>米在庫管理</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="./style/BasicStyle.css">
    <link rel="stylesheet" href="./style/kanri.css">
    <link href="https://use.fontawesome.com/releases/v5.13.0/css/all.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"
    integrity="sha384-vk5WoKIaW/vJyUAd9n/wmopsmNhiy+L2Z+SBxGYnUkunIxVxAv/UtMOhba/xskxh"
    crossorigin="anonymous"></script>
    <script type="text/javascript" src="./script/partsscript.js"></script>
  </head>
  <body>

    <button class="mt-1">
    <a href="logout.php?logout">ログアウト</a>
    </button>
          <h1 class="text-center mb-3">はい票箋(仮)</h1>
    <div class="SearchMenu mb-4">
      <div class="SearchMenu_header">検索条件変更</div>
      <div class="searchMenu_Inner">
        <form class="form-kanri mb-4" method="POST">
          <div class="form-group">
            <label for="iNensan">年産</label>
            <select class="form-control Nensan" id="iNensan" name="Nensan">
            <?php
            foreach ($NensanList as $key => $Nensan) {
                  echo '<option name="" value="' .htmlspecialchars($key) .'">'
                  . htmlspecialchars($Nensan).'</option>';
            }
            ?>
            </select>
          </div>
          <div class="form-group mb-2">
            <label for="iHinsyu" class="col-form-label">品種</label>
            <select class="form-control Hinsyu" id="iHinsyu" name="Hinsyu">
                <option name="" value="" selected>選択してください</option>
                <?php
                foreach ($HinsyuList as $key => $Hinsyu) {
                      echo '<option name="" value="' .htmlspecialchars($key) .'">'
                      . htmlspecialchars($Hinsyu).'</option>';
                }
                ?>
            </select>
          </div>
          <div class="form-group" id="id_toukyu">
            <label for="iToukyu" class="col-form-label">等級</label>
            <select class="form-control Toukyu" id="iToukyu" name="Toukyu">
                <option name="" value="" selected>選択してください</option>
                <?php
                foreach ($ToukyuList as $key => $Toukyu) {
                      echo '<option name="" value="' .htmlspecialchars($key) .'">'
                      . htmlspecialchars($Toukyu).'</option>';
                }
                ?>
            </select>
          </div>
          <button type="submit" class="btn btn-primary mt-3 btn-block search" name="search">検索</button>
        </form>
      </div>
    </div>
    <div class="AddButton">
        <button class="btn btn-primary" onclick="location.href='/home.php'">個別検索</button>
    </div>
    <?php
        if (isset($inventoryData) && count($inventoryData)>0) { ?>
    <table class="table table-bordered table-hover table-sm bg-white">
      <thead>
        <th>種類</th>
        <th>年産</th>
        <th>銘柄</th>
        <th>等級</th>
        <th>量目</th>
        <th>包装</th>
      </thead>
      <tbody>
        <?php
          if(substr($inventoryData[0]['Hinsyu_NAME'],-1,2)=="もち"){
            $syurui = "水稲もち玄米";
          }else{
            $syurui = "水稲うるち玄米";
          } ?>
        <td><?php echo($syurui); ?></td>
        <td><?php echo(htmlspecialchars(wareki($inventoryData[0]['Nensan'])));?></td>
        <td><?php echo(htmlspecialchars($inventoryData[0]['Hinsyu_NAME'])); ?></td>
        <td><?php echo(htmlspecialchars($inventoryData[0]['Toukyu']));?></td>
        <td>30k</td>
        <td>紙袋</td>
      </tbody>
    </table>
    <?php }elseif(isset($inventoryData) && count($inventoryData)==0){
        echo('<p>年産：'. wareki($_SESSION['parmNensan']));
        if(!empty($_SESSION['parmHinsyu'])){
          echo('&nbsp&nbsp&nbsp&nbsp品種：'.$HinsyuList[$_SESSION['parmHinsyu']]);
        }else{
          echo('&nbsp&nbsp&nbsp&nbsp品種：―');
        }
        if(!empty($_SESSION['parmToukyu'])){
          echo('&nbsp&nbsp&nbsp&nbsp等級：'.$ToukyuList[$_SESSION['parmToukyu']]);
        }else{
          echo('&nbsp&nbsp&nbsp&nbsp等級：―');
        }
        echo('&nbsp&nbsp&nbsp&nbsp※該当データなし※</p>');
    }
    ?>

    <table class="table table-bordered table-hover table-sm bg-white">
      <thead class="bg-success">
        <tr>
          <th scope="col">月日</th>
          <th scope="col">生産者</th>
          <th scope="col">入庫数</th>
          <th scope="col">出庫数</th>
          <th scope="col">在庫数</th>
          <th class="PcCol" scope="col">備考</th>
        </tr>
      </thead>
      <tbody>
      <?php if (isset($inventoryData[0]['No'])) {
            //検索結果の配列に検索条件の値も含まれるため、入出荷情報の要素がNullかどうかをチェックする。
            $zaiko = 0;
            foreach ($inventoryData as $row) :
                //在庫数
                $zaiko = $zaiko + (float)$row['ArrivalQuantity'] - (float)$row['ShipmentQuantity'];
                //モーダルウィンドウ用ターゲットID(No)
                $targetid = trim($row['No']);
                ?>
        <tr class="modal-open" data-target=<?php echo htmlspecialchars($targetid); ?>>
          <td><?php echo htmlspecialchars($row['InOutDate']); ?></td>
          <?php
            $arrival = htmlspecialchars($row['ArrivalQuantity']);
            $shipment = htmlspecialchars($row['ShipmentQuantity']);
            $tekiyou = "";
            $tekiyou = $row['Seisansya_NAME'];
          ?>
          <td><?php echo $tekiyou; ?></td>
          <td><?php echo htmlspecialchars($row['ArrivalQuantity']); ?></td>
          <td><?php echo htmlspecialchars($row['ShipmentQuantity']); ?></td>
          <td><?php echo htmlspecialchars($zaiko); ?></td>
          <td class="PcCol"><?php echo htmlspecialchars($row['Bikou']); ?></td>
        </tr>
            <!--更新モーダルウィンドウ-->
          <div id=<?php echo htmlspecialchars($targetid); ?> class="modal">
            <div class="modal_bg modal-close"></div>
            <div class="modal_content">
              <div id="details" class="mb-4">
              <h4>詳細</h4>
                <!--No 隠し項目-->
                <input type="hidden" name="No" value="<?php echo htmlspecialchars($row['No']); ?>">
                <div class="">
                    <!--生産者名-->
                    <div class="row">
                      <div class="col-4">生産者名</div>
                      <div class="col-4"><?php echo htmlspecialchars($row['Seisansya_NAME']); ?></div>
                    </div>
                    <!--銘柄-->
                    <div class="row">
                      <div class="col-4">銘柄</div>
                      <div class="col-4"><?php echo htmlspecialchars($row['Hinsyu_NAME']); ?></div>
                    </div>
                    <!--等級-->
                    <div class="row">
                      <div class="col-4">等級</div>
                      <div class="col-4"><?php echo htmlspecialchars($row['Toukyu']); ?></div>
                    </div>
                    <!--入出庫日-->
                    <div class="row">
                      <div class="col-4">入出庫日</div>
                      <div class="col-4"><?php echo htmlspecialchars($row['InOutDate']); ?></div>
                    </div>
                    <!--入庫数-->
                    <div class="row">
                      <div class="col-4">入庫数</div>
                      <div class="col-4"><?php echo htmlspecialchars($row['ArrivalQuantity']); ?></div>
                    </div>
                    <!--出庫数-->
                    <div class="row">
                      <div class="col-4">出庫数</div>
                      <div class="col-4"><?php echo htmlspecialchars($row['ShipmentQuantity']); ?></div>
                    </div>
                    <!--備考-->
                    <div class="row">
                      <div class="col-4">備考</div>
                      <div class="col-4"><?php echo htmlspecialchars($row['Bikou']); ?></div>
                    </div>
                </div>
              </div>
            </div>
          </div>
          <!--更新用モーダルウィンドウここまで-->
            <?php endforeach; ?>
      <?php } ?>
      </tbody>
    </table>

    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
      integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
      crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
      integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6"
      crossorigin="anonymous"></script>

  </body>
</html>
