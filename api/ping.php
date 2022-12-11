<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Disposition, Content-Type, Content-Length, Accept-Encoding");
header("Content-type:application/json");
$res = `ping -c5 home.hide.li`;
preg_match('/\d+\.\d+\/\d+\.\d+\/\d+\.\d+\/\d+\.\d+/', $res, $matches);
$resarr = explode("/", $matches[0]);
$arr = [];
$arr["min"] = $resarr[0];
$arr["avg"] = $resarr[1];
$arr["max"] = $resarr[2];
$arr["mdev"] = $resarr[3];
$arr["raw"] = $res;
echo json_encode($arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
