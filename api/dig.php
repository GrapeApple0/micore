<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Disposition, Content-Type, Content-Length, Accept-Encoding");
header("Content-type:application/json");
$ipv4_regex = "/^(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/";
$ipv6_regex = "/(([0-9a-fA-F]{1,4}:){7,7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}|([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}|([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}|([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})|:((:[0-9a-fA-F]{1,4}){1,7}|:)|fe80:(:[0-9a-fA-F]{0,4}){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])|([0-9a-fA-F]{1,4}:){1,4}:((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9]))/";
if (is_string(filter_input(INPUT_GET, "domain"))) {
	$domain = htmlspecialchars(filter_input(INPUT_GET, "domain"), ENT_QUOTES, 'UTF-8');
	if (preg_match($ipv4_regex, $domain)) {
		$ip = $domain;
		$domain = implode(".", array_reverse(explode(".", $domain))) . ".in-addr.arpa";
	} else if (preg_match($ipv6_regex, $domain)) {
		$ipv6 = $ip;
		$prefix_length = 128;
		$ip = $domain;
		$addr = inet_pton($ip);
		$unpack = unpack('H*hex', $addr);
		$hex = $unpack['hex'];
		$arpa = implode('.', array_reverse(str_split($hex))) . '.ip6.arpa';
		$domain = $arpa;
	}
	$arr = [];
	$arr["status"] = 0;
	$result = dns_get_record("$domain", DNS_A | DNS_AAAA | DNS_MX | DNS_NS | DNS_CAA | DNS_PTR | DNS_SOA | DNS_SRV | DNS_TXT | DNS_CNAME | DNS_HINFO | DNS_NAPTR);
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
	if ($ip != "") {
		$arr["ip"] = $ip;
	}
	$arr["result"] = $result;
	echo json_encode($arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} else {
	$arr = [];
	$arr["status"] = 1;
	$arr["raw"] = "Domain is not match pattern.";
	echo json_encode($arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}
