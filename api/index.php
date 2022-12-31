<?php
$url = ltrim(htmlspecialchars($_SERVER["REQUEST_URI"], ENT_QUOTES, 'UTF-8'), "/");
$page = parse_url($url)["path"];
if (file_exists(__DIR__ . "/$page.php")) {
	require_once(__DIR__ . "/$page.php");
} else {
	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	header("Access-Control-Allow-Headers: Content-Disposition, Content-Type, Content-Length, Accept-Encoding");
	header("Content-type:application/json");
	$arr = [];
	$arr["status"] = 1;
	$arr["message"] = "Page not found.";
	$arr["page"] = $page;
	echo json_encode($arr);
}
