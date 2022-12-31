<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Disposition, Content-Type, Content-Length, Accept-Encoding");
header("Content-type:application/json");
$config = include(dirname(__DIR__) . '/config.php');

$ch = curl_init();
$id = filter_input(INPUT_GET, "id");
if (is_string($id)) {
	curl_setopt($ch, CURLOPT_URL, "https://api.twitter.com/1.1/statuses/show/$id.json");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	$headers = array();
	$headers[] = 'Authorization: Bearer ' . $config["bearer"];
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	$result = curl_exec($ch);
	curl_close($ch);
	echo $result;
}
