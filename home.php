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
    //セッションに保存しておく
    $_SESSION['Inventory_ID'] = htmlspecialchars($inventoryData[0]['Inventory_ID']);
    $_SESSION['Seisansya'] = htmlspecialchars($inventoryData[0]['Seisansya_ID']);
    $_SESSION['Toukyu'] = htmlspecialchars($inventoryData[0]['Toukyu_ID']);
    $_SESSION['Hinsyu'] = htmlspecialchars($inventoryData[0]['Hinsyu_ID']);
    $_SESSION['Nensan'] = htmlspecialchars($inventoryData[0]['Nensan']);
}
//入出荷情報追加
if (isset($_POST['inventoryDataAdd'])) {
    include_once('InsertData.php');
    $inventory_ID = $_SESSION['Inventory_ID'];
    $inventoryData = setData($_POST, $db, $inventory_ID);
    //データ再取得
    $inventoryData = getData($_SESSION, $db);
}
//入出荷情報更新
if (isset($_POST['inventoryDataUp'])) {
    include_once('UpdateData.php');
    $upData = ChangeData($_POST, $db);
    //データ再取得
    $inventoryData = getData($_SESSION, $db);
}
//米情報追加
if (isset($_POST{'Riceadd'})) {
    include_once('inserthead.php');
    $inserthead = sethead($_POST, $db);
    if ($inserthead == "Error") {
        echo '<script type="text/javascript"> alert("登録失敗エラー");</script>';
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
    <script type="text/javascript" src="./script/pludown.js"></script>
    <script type="text/javascript" src="./script/partsscript.js"></script>
  </head>
  <body>

    <button class="mt-1">
    <a href="logout.php?logout">ログアウト</a>
    </button>
          <h1 class="text-center mb-3">米在庫管理</h1>
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
          <div class="form-group">
            <label for="iSeisansya">生産者</label>
            <select class="form-control Seisansya" id="iSeisansya" name="Seisansya" required>
              <option selected disabled>選択してください</option>
            </select>
          </div>
          <div class="form-group mb-2">
            <label for="iHinsyu" class="col-form-label">品種</label>
            <select class="form-control Hinsyu" id="iHinsyu" name="Hinsyu" disabled="true" required>
              <option selected disabled>選択してください</option>
            </select>
          </div>
          <div class="form-group" id="id_toukyu">
            <label for="iToukyu" class="col-form-label">等級</label>
            <select class="form-control Toukyu" id="iToukyu" name="Toukyu" disabled="true" required>
              <option selected disabled>選択してください</option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary mt-3 btn-block search" name="search">検索</button>
        </form>
      </div>
    </div>
    <div class="AddButton">
      <?php if (isset($inventoryData)) { ?>
        <button class="btn btn-success modal-open" data-target="inventory-add">入出荷追加</button>
      <?php }?>
        <button class="btn btn-info text-right modal-open" data-target="Rice-add">米情報追加</button>
        <button class="btn btn-primary" onclick="location.href='/alldata.php'">一括検索</button>
    </div>
    <?php
    if (isset($inventoryData) && count($inventoryData)>0) {
      echo '<p>年産：'. htmlspecialchars(wareki($inventoryData[0]['Nensan'])) .
      '&nbsp&nbsp&nbsp&nbsp生産者：' . htmlspecialchars($inventoryData[0]['Seisansya_NAME']) .
      '&nbsp&nbsp&nbsp&nbsp等級：' . htmlspecialchars($inventoryData[0]['Toukyu']) .
      '&nbsp&nbsp&nbsp&nbsp品種：' . htmlspecialchars($inventoryData[0]['Hinsyu_NAME']) . '</p>';
    }elseif(isset($inventoryData) && count($inventoryData)==0){
      echo('<p>年産：'. wareki($_SESSION['parmNensan']));
      echo('&nbsp&nbsp&nbsp&nbsp品種：'.$HinsyuList[$_SESSION['parmHinsyu']]);
      echo('&nbsp&nbsp&nbsp&nbsp等級：'.$ToukyuList[$_SESSION['parmToukyu']]);
      echo('&nbsp&nbsp&nbsp&nbsp※該当データなし※</p>');
    }
      ?>
    <table class="table table-bordered table-hover table-sm bg-white">
      <thead class="bg-success">
        <tr>
          <th scope="col">入出荷日</th>
          <th scope="col">入荷数</th>
          <th scope="col">出荷数</th>
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
          <td><?php echo htmlspecialchars($row['ArrivalQuantity']); ?></td>
          <td><?php echo htmlspecialchars($row['ShipmentQuantity']); ?></td>
          <td><?php echo htmlspecialchars($zaiko); ?></td>
          <td class="PcCol"><?php echo htmlspecialchars($row['Bikou']); ?></td>
        </tr>
          <!--更新モーダルウィンドウ-->
          <div id=<?php echo htmlspecialchars($targetid); ?> class="modal">
            <div class="modal_bg modal-close"></div>
            <div class="modal_content">
            <form id="UpData" class="mb-4" method="POST">
              <h4>入力情報更新</h4>
              <!--No 隠し項目-->
              <input type="hidden" name="No" value="<?php echo htmlspecialchars($row['No']); ?>">
              <!--入出荷日-->
              <div class="form-group" id="UpOutDate">
                <label for="date" class="col-form-label">入出荷日</label>
                <input type="date" class="form-control" name="UpInOutDate"
                value="<?php echo htmlspecialchars($row['InOutDate']); ?>" readonly>
              </div>
              <!--入荷数-->
              <div class="form-group" id="UpArrival">
                <label for="Arrival" class="col-form-label">入荷数</label>
                <input type="number" class="form-control Arrival" name="UpArrivalQuantity"
                step="0.1" min="0" max="9000" value="<?php echo htmlspecialchars($row['ArrivalQuantity']); ?>">
              </div>
              <!--出荷数-->
              <div class="form-group" id="Shipment">
                <label for="Shipment" class="col-form-label mr-3">出荷数</label>
                <input type="number" class="form-control Shipment" name="UpShipmentQuantity" step="0.1" min="0" max="9000"
                value="<?php echo htmlspecialchars($row['ShipmentQuantity']); ?>">
              </div>
              <!--備考-->
              <div class="form-group" id="Bikou">
                <label for="Bikou" class="col-form-label mr-3">備考</label>
                <input type="text" id="Bikou" class="form-control" name="UpBikou"
                value="<?php echo htmlspecialchars($row['Bikou']); ?>">
              </div>
              <p>※削除したい場合は入荷数、出荷数を0にしてください。</p>
              <div class="form-groupa row btnrow" id="addbtn">
                <div class="col-md-6">
                  <button type="submit" class="btn btn-success mt-3 btn-block inventoryDataUp" name="inventoryDataUp">
                    更新
                  </button>
                </div>
                <div class="col-md-6">
                  <button class="btn btn-primary modal-close mt-3 btn-block">閉じる</button>
                </div>
              </div>
            </form>
            </div>
          </div>
          <!--更新用モーダルウィンドウここまで-->
            <?php endforeach; ?>
      <?php } ?>
      </tbody>
    </table>
    <!--入出荷情報追加フォーム-->
    <div id="inventory-add" class="modal">
        <div class="modal_bg modal-close"></div>
        <div class="modal_content">
            <form id="DataAdd" class="mb-4" method="POST">
              <div class="form-group" id="InOutDate">
                <label for="date" class="col-form-label">入出荷日</label>
                <input type="date" id="date" class="form-control InOutDate" name="NewInOutDate" required>
              </div>
              <div class="form-group" id="Arrival">
                <label for="Arrival" class="col-form-label">入荷数</label>
                <input type="number" class="form-control Arrival" name="NewArrivalQuantity" step="0.1" min = "-99" max="9000">
              </div>
              <div class="form-group" id="Shipment">
                <label for="Shipment" class="col-form-label mr-3">出荷数</label>
                <input type="number" class="form-control Shipment" name="NewShipmentQuantity" step="0.1" min = "-99" max = "9000">
              </div>
              <div class="form-group" id="Bikou">
                <label for="Bikou" class="col-form-label mr-3">備考</label>
                <input type="text" id="Bikou" class="form-control" name="NewBikou">
              </div>
              <div class="form-groupa row btnrow" id="addbtn">
                <div class="col-md-6">
                  <button type="submit" class="btn btn-success mt-3 btn-block inventoryDataAdd" name="inventoryDataAdd">
                    登録
                  </button>
                </div>
                <div class="col-md-6">
                  <button class="btn btn-primary modal-close mt-3 btn-block">閉じる</button>
                </div>
              </div>
            </form>
        </div><!--modal_content-->
    </div><!--modal-->

    <!--米情報登録フォーム-->
    <div id="Rice-add" class="modal">
        <div class="modal_bg modal-close"></div>
        <div class="modal_content">
            <form id="RiceAdd" class="mb-4" method="POST">
              <div class="form-group" id="AddSeisansya">
                <label for="iSeisansya" class="col-form-label">生産者</label>
                <select class="form-control AddSeisansya" name="AddSeisansya" required>
                  <option value="" selected disabled>選択してください</option>
                  <?php
                    foreach ($SeisansyaList as $key => $Seisansya_NAME) {
                        echo '<option name="" value="' .htmlspecialchars($key) .'">'
                         . htmlspecialchars($Seisansya_NAME).'</option>';
                    }
                    ?>
                  <option name="" value="999">その他</option>
                </select>
                <input type="text" class="form-control SeisansyaText mt-2" id="SeisansyaText" name="SeisansyaText"
                placeholder="その他の場合はここに生産者名を入力してください。">
              </div>
              <div class="form-group" id="AddHinsyu">
                <label for="AddHinsyu" class="col-form-label mr-3">品種</label>
                <select class="form-control AddHinsyu" name="AddHinsyu" required>
                  <option value="" selected disabled>選択してください</option>
                  <?php
                    foreach ($HinsyuList as $key => $Hinsyu_NAME) {
                        echo '<option name="" value="' .htmlspecialchars($key) .'">'
                         . htmlspecialchars($Hinsyu_NAME).'</option>';
                    }
                    ?>
                </select>
              </div>
              <div class="form-group" id="AddTouku">
                <label for="AddTouku" class="col-form-label">等級</label>
                <select class="form-control" name="AddTouku" required>
                  <option value="" selected disabled>選択してください</option>
                    <?php
                    foreach ($ToukyuList as $key => $Toukyu) {
                        echo '<option name="" value="' .htmlspecialchars($key) .'">'
                         . htmlspecialchars($Toukyu).'</option>';
                    }
                    ?>
                </select>
              </div>
              <div class="form-group" id="AddNensan">
                <label for="AddNensan" class="col-form-label mr-3">年産</label>
                <select class="form-control" name="AddNensan" required>
                  <?php
                    foreach ($yearList as $year) {
                        if ($year == $kijyun) {
                            echo '<option name="" selected value="' . htmlspecialchars($year) . '">'
                              . htmlspecialchars(wareki($year)) . '</option>';
                        } else {
                            echo '<option name="" value="' . htmlspecialchars($year) . '">'
                              . htmlspecialchars(wareki($year)) . '</option>';
                        }
                    } ?>
                </select>
              </div>
              <div class="form-group row btnrow" id="addbtn">
                <div class="col-md-6">
                  <button type="submit" class="btn btn-success mt-3 btn-block" name="Riceadd">登録</button>
                </div>
                <div class="col-md-6">
                  <button class="btn btn-primary modal-close mt-3 btn-block">閉じる</button>
                </div>
              </div>
            </form>
        </div><!--modal_content-->
    </div><!--modal-->

    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
      integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
      crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
      integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6"
      crossorigin="anonymous"></script>

  </body>
</html>
