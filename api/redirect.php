<?php


try {
	header('Content-Type: application/json; charset=UTF-8');
	function expand_url($url)
	{
		$headers = get_headers($url, 1);

		if (isset($headers['Location'])) {
			if (is_array($headers['Location'])) {
				return make_apath($url, array_pop($headers['Location']));
			} else {
				return make_apath($url, $headers['Location']);
			}
		} else {
			return $url;
		}
	}

	function make_apath($base = '', $rel_path = '')
	{
		$base = preg_replace('/\/[^\/]+$/', '/', $base);
		$parse = parse_url($base);
		if (preg_match('/^https\:\/\//', $rel_path)) {
			return $rel_path;
		} elseif (preg_match('/^\/.+/', $rel_path)) {
			$out = $parse['scheme'] . '://' . $parse['host'] . $rel_path;
			return $out;
		}
		$tmp = array();
		$a = array();
		$b = array();
		$tmp = preg_split("/\//", $parse['path']);
		foreach ($tmp as $v) {
			if ($v) {
				array_push($a, $v);
			}
		}
		$b = preg_split("/\//", $rel_path);
		foreach ($b as $v) {
			if (strcmp($v, '') == 0) {
				continue;
			} elseif ($v == '.') {
			} elseif ($v == '..') {
				array_pop($a);
			} else {
				array_push($a, $v);
			}
		}
		$path = join('/', $a);
		return '/' . $path;
	}

	if (filter_input(INPUT_POST, "url") != "") {
		$url = htmlspecialchars(filter_input(INPUT_POST, "url"));
		if ($url == "" || !preg_match('/https?:\/{2}[\w\/:%#\$&\?\(\)~\.=\+\-]+/', $url)) {
			$res["status"] = 1;
			$res["result"] = "Error: URL is not found or not match regex.";
		} else {
			$res["status"] = 0;
			$res["result"] = expand_url($url);
		}
		echo json_encode($res);
	} else if (filter_input(INPUT_GET, "url") != "") {
		$url = htmlspecialchars(filter_input(INPUT_GET, "url"));
		if ($url == "" || !preg_match('/https?:\/{2}[\w\/:%#\$&\?\(\)~\.=\+\-]+/', $url)) {
			$res["status"] = 1;
			$res["result"] = "Error: URL is not found or not match regex.";
		} else {
			$res["status"] = 0;
			$res["result"] = expand_url($url);
		}
		echo json_encode($res);
	} else {
		$res["status"] = 1;
		$res["result"] = "Error: URL is not found.";
		echo json_encode($res);
	}
} catch (\Throwable $th) {
	var_dump($th);
}
