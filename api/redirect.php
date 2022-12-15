<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Disposition, Content-Type, Content-Length, Accept-Encoding");
header("Content-type:application/json");

function expand_url($url)
{
	$headers = get_headers($url, 1);
	if ($headers['Location'] != null || $headers['location'] != null) {
		if ($headers['location'] != null) {
			if (parse_url($url)["host"] == parse_url($headers['location'])["host"]) {
				return $headers['location'];
			} else {
				return $headers['location'];
			}
		} else if ($headers['Location'] != null) {
			if (parse_url($url)["host"] == parse_url($headers['Location'])["host"]) {
				return $headers['Location'];
			} else {
				return $headers['Location'];
			}
		} else {
			return $url;
		}
	} else {
		return $url;
	}
}

function pathToUrl($pPath, $pUrl)
{
	$path = trim($pPath);
	$url = trim($pUrl);
	if ($path === '') {
		return $url;
	}
	if (
		stripos($path, 'http://') === 0 ||
		stripos($path, 'https://') === 0 ||
		stripos($path, 'mailto:') === 0
	) {
		return $path;
	}
	if (strpos($path, '#') === 0) {
		return $url . $path;
	}
	$urlAry = explode('/', $url);
	if (!isset($urlAry[2])) {
		return false;
	}
	if (strpos($path, '//') === 0) {
		return $urlAry[0] . $path;
	}
	$urlHome = $urlAry[0] . '//' . $urlAry[2];
	if (!$pathBase = parse_url($url, PHP_URL_PATH)) {
		$pathBase = '/';
	}
	if (strpos($path, '?') === 0) {
		return $urlHome . $pathBase . $path;
	}
	if (strpos($path, '/') === 0) {
		return $urlHome . $path;
	}
	$pathBaseAry = array_filter(explode('/', $pathBase), 'strlen');
	if (strpos(end($pathBaseAry), '.') !== false) {
		array_pop($pathBaseAry);
	}

	foreach (explode('/', $path) as $pathElem) {
		if ($pathElem === '.') {
			continue;
		}
		if ($pathElem === '..') {
			array_pop($pathBaseAry);
			continue;
		}
		if ($pathElem !== '') {
			$pathBaseAry[] = $pathElem;
		}
	}

	return (substr($path, -1) === '/') ? $urlHome . '/' . implode('/', $pathBaseAry) . '/'
		: $urlHome . '/' . implode('/', $pathBaseAry);
}

$res = [];
if (filter_input(INPUT_POST, "url") != "") {
	$url = htmlspecialchars(filter_input(INPUT_POST, "url"));
	if ($url == "" || !preg_match('/https?:\/{2}[\w\/:%#\$&\?\(\)~\.=\+\-]+/', $url)) {
		$res["status"] = 1;
		$res["result"] = "Error: URL is not found or not match regex.";
	} else {
		$res["status"] = "0";
		$res["result"] = pathToUrl(expand_url($url), $url);
		if ($res["result"] == $url) {
			$res["status"] = "2";
			$res["result"] = "Info: This URL is not redirect.";
		}
	}
	echo json_encode($res);
} else if (filter_input(INPUT_GET, "url") != "") {
	$url = htmlspecialchars(filter_input(INPUT_GET, "url"));
	if (is_string($url)) {
		if ($url == "" || !preg_match('/https?:\/{2}[\w\/:%#\$&\?\(\)~\.=\+\-]+/', $url)) {
			$res["status"] = "1";
			$res["result"] = "Error: URL is not found or not match regex.";
		} else {
			$res["status"] = "0";
			$res["result"] = pathToUrl(expand_url($url), $url);
			if ($res["result"] == $url) {
				$res["status"] = 2;
			}
		}
		echo json_encode($res);
	} else {
		$res["status"] = "1";
		$res["result"] = "Error: URL is not string.";
		echo json_encode($res);
	}
} else {
	$res["status"] = "1";
	$res["result"] = "Error: URL is not found.";
	echo json_encode($res);
}
