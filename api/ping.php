<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Disposition, Content-Type, Content-Length, Accept-Encoding");
header("Content-type:application/json");
if (is_string(filter_input(INPUT_GET, "domain"))) {
	$domain = htmlspecialchars(filter_input(INPUT_GET, "domain"), ENT_QUOTES, 'UTF-8');
	$res = `ping -c5 $domain`;
	preg_match('/\d+\.\d+\/\d+\.\d+\/\d+\.\d+\/\d+\.\d+/', $res, $matches);
	$resarr = explode("/", $matches[0]);
	$arr = [];
	$arr["status"] = 0;
	$arr["min"] = $resarr[0];
	$arr["avg"] = $resarr[1];
	$arr["max"] = $resarr[2];
	$arr["mdev"] = $resarr[3];
	$arr["raw"] = $res;
	echo json_encode($arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} else {
	$arr = [];
	$arr["status"] = 1;
	$arr["raw"] = "Domain is not match pattern.";
	echo json_encode($arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}
