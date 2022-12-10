<?php
//misskey.04.si/api/meta
//mstdn.04.si/api/v1/instance
header("Content-type:application/json");
$instance_url = filter_input(INPUT_GET, "domain"); // インスタンスのURL
$api_url = $instance_url . "/api/meta"; // インスタンス情報を取得するAPIのURL

$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
$response = curl_exec($ch);
curl_close($ch);

$instance_info = json_decode($response, true); // インスタンス情報を取得
echo $response;
