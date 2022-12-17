<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Disposition, Content-Type, Content-Length, Accept-Encoding");
header("Content-type:application/json");
if (is_string(filter_input(INPUT_GET, "domain"))) {
	$domain = htmlspecialchars(filter_input(INPUT_GET, "domain"), ENT_QUOTES, 'UTF-8');
	$arr = [];
	$arr["status"] = 0;
	$result = dns_get_record("$domain", DNS_ANY);
	$records = [];
	foreach ($result as $record) {
		if ($record["type"] == "A") {
			$resarr = [];
			$resarr["type"] = "A";
			$resarr["ttl"] = $record['ttl'];
			$resarr["record"] = $record['ip'];
			array_push($records, $resarr);
		} else if ($record["type"] == "AAAA") {
			$resarr = [];
			$resarr["type"] = "AAAA";
			$resarr["ttl"] = $record['ttl'];
			$resarr["record"] = $record['ipv6'];
			array_push($records, $resarr);
		} else if ($record["type"] == "TXT") {
			$resarr = [];
			$resarr["type"] = "TXT";
			$resarr["ttl"] = $record['ttl'];
			$resarr["record"] = $record['txt'];
			array_push($records, $resarr);
		} else if ($record["type"] == "NS") {
			$resarr = [];
			$resarr["type"] = "NS";
			$resarr["ttl"] = $record['ttl'];
			$resarr["record"] = $record['target'];
			array_push($records, $resarr);
		} else if ($record["type"] == "MX") {
			$resarr = [];
			$resarr["type"] = "MX";
			$resarr["ttl"] = $record['ttl'];
			$resarr["priority"] = $record['pri'];
			$resarr["record"] = $record['target'];
			array_push($records, $resarr);
		} else if ($record["type"] == "CAA") {
			$resarr = [];
			$resarr["type"] = "CAA";
			$resarr["ttl"] = $record['ttl'];
			$resarr["flags"] = $record['flags'];
			$resarr["tag"] = $record['tag'];
			$resarr["record"] = $record['value'];
			array_push($records, $resarr);
		}
	}
	$arr["domain"] = $domain;
	$arr["result"] = $result;
	echo json_encode($arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} else {
	$arr = [];
	$arr["status"] = 1;
	$arr["raw"] = "Domain is not match pattern.";
	echo json_encode($arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}
