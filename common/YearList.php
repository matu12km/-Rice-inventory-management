<?php
function getYearList($kijyun)
{
    $yearList = array();
    for ($i = -5; $i < $kijyun + 10; $i ++) {
        $yearList[] = $kijyun + $i;
    }
    return $yearList;
}
