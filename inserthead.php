<?php
function sethead($params, $db)
{
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    //生産者でその他を選択している場合、生産者マスタに登録する
    if ($params['AddSeisansya'] == "999") {
        try {
            $Seisansya = trim($params['SeisansyaText']);
            $insertSeisanSQL = "INSERT INTO m_seisansya (Seisansya_NAME) VALUES(:Seisansya_NAME)";
            $in = $db->prepare($insertSeisanSQL);
            $in->bindvalue(':Seisansya_NAME', $Seisansya, PDO::PARAM_STR);
            $in->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
            return "Error";
        }
        //生産者マスタを検索し登録した生産者の生産者IDを取得する。
        try {
            $sseisanid = "SELECT Seisansya_ID FROM m_seisansya WHERE Seisansya_NAME =:Seisansya";
            $ss = $db->prepare($sseisanid);
            $ss->bindvalue(':Seisansya', $Seisansya, PDO::PARAM_STR);
            $ss->execute();
            $count = $ss->rowcount();
            if ($count == 1) {
                while ($row = $ss -> fetch(PDO::FETCH_ASSOC)) {
                    $Seisansya = $row['Seisansya_ID'];
                }
            } else {
                die();
                return "Error";
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
            return "Error";
        }
    } else { //生産者をプルダウンから選択している場合はプルダウンから
        $Seisansya = $params['AddSeisansya'];
    }
    //等級
    $Toukyu = $params['AddTouku'];
    //品種
    $Hinsyu = $params['AddHinsyu'];
    //年産
    $Nensan = $params['AddNensan'];

    try {
        $sql = " INSERT INTO inventoryhead (Seisansya_ID, Toukyu_ID, Hinsyu_ID, Nensan)";
        $sql.= " VALUES(:Seinsasya_ID, :Toukyu_ID, :Hinsyu_ID, :Nensan)";

        $stmt = $db->prepare($sql);
        $stmt->bindvalue(':Seinsasya_ID', $Seisansya, PDO::PARAM_STR);
        $stmt->bindvalue(':Toukyu_ID', $Toukyu, PDO::PARAM_STR);
        $stmt->bindvalue(':Hinsyu_ID', $Hinsyu, PDO::PARAM_STR);
        $stmt->bindvalue(':Nensan', $Nensan, PDO::PARAM_STR);
        $stmt->execute();
    } catch (PDOException $e) {
        echo $e->getMessage();
        die();
        return "Error";
    }
    return "success";
}
